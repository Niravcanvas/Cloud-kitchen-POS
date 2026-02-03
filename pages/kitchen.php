<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* ── AJAX status-update handler ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status   = $_POST['status'];
    $allowed  = ['Pending', 'Preparing', 'Completed', 'Cancelled'];

    if ($order_id && in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        echo $stmt->execute()
            ? json_encode(['success' => true])
            : json_encode(['success' => false, 'msg' => 'DB error']);
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'msg' => 'Invalid data']);
    }
    exit();
}

/* ── Fetch active orders ── */
$orders = [];
$sql    = "SELECT o.id AS order_id, o.order_time, o.status, c.name AS customer_name
           FROM orders o
           LEFT JOIN customers c ON o.customer_id = c.id
           WHERE o.status IN ('Pending','Preparing')
           ORDER BY o.order_time DESC";
$result = $conn->query($sql);

while ($order = $result->fetch_assoc()) {
    /* items – prepared statement */
    $stmt = $conn->prepare("SELECT i.name, oi.quantity FROM order_items oi JOIN items i ON oi.item_id = i.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order['order_id']);
    $stmt->execute();
    $res_items = $stmt->get_result();

    $items = [];
    while ($item = $res_items->fetch_assoc()) {
        $items[] = $item;
    }
    $order['items'] = $items;
    $orders[]       = $order;
    $stmt->close();
}

/* ── Compute summary counts for stats bar ── */
$countPending   = 0;
$countPreparing = 0;
foreach ($orders as $o) {
    if ($o['status'] === 'Pending')   $countPending++;
    if ($o['status'] === 'Preparing') $countPreparing++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kitchen Orders</title>
<link rel="stylesheet" href="static/style.css">
<link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ─── CSS Variables ─── */
:root {
  --bg-primary:       #FBF9F5;
  --bg-secondary:     #CEC3C1;
  --accent-primary:   #630116;
  --accent-secondary: #AF5B73;
  --accent-success:   #7E892B;
  --text-dark:        #2a2a2a;
  --text-muted:       #6a6a6a;
  --shadow-rest:      0 2px 8px rgba(99,1,22,0.04);
  --shadow-hover:     0 8px 24px rgba(99,1,22,0.12);
  --radius-sm:        6px;
  --radius-md:        8px;

  /* status colours */
  --status-pending:   #d97706;   /* amber */
  --status-pending-bg: rgba(217,119,6,0.08);
  --status-preparing: var(--accent-secondary); /* rose */
  --status-preparing-bg: rgba(175,91,115,0.1);
  --status-completed: var(--accent-success);
  --status-completed-bg: rgba(126,137,43,0.1);
  --status-cancelled: #6b7280;
  --status-cancelled-bg: rgba(107,114,128,0.1);
}

/* ─── Reset ─── */
*,*::before,*::after { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  color: var(--text-dark);
  min-height: 100vh;
}

/* ─── Layout ─── */
.content-wrapper {
  margin-left: 260px;
  min-height: 100vh;
  padding: 48px 40px;
  transition: margin-left 0.3s ease;
}
.main-content { max-width: 1200px; margin: 0 auto; }

/* ─── Page Header ─── */
.page-header { margin-bottom: 28px; animation: fadeIn 0.5s ease-out; }
.page-header h1 {
  font-family: 'Crimson Pro', serif;
  font-size: 38px; font-weight: 600;
  color: var(--accent-primary);
  letter-spacing: -0.5px; margin-bottom: 6px;
}
.page-header p { font-size: 15px; color: var(--text-muted); }

/* ═══════════════════════════════════════════
   STATS BAR
   ═══════════════════════════════════════════ */
.stats-bar {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 32px;
  animation: fadeIn 0.5s ease-out 0.08s backwards;
}
.stat-card {
  background: #fff;
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-md);
  padding: 20px 22px;
  box-shadow: var(--shadow-rest);
  display: flex; align-items: center; gap: 16px;
  transition: box-shadow 0.3s ease;
}
.stat-card:hover { box-shadow: var(--shadow-hover); }

.stat-icon {
  width: 46px; height: 46px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.stat-icon svg {
  width: 22px; height: 22px;
  fill: none; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
}

.stat-icon--pending   { background: var(--status-pending-bg); }
.stat-icon--pending svg { stroke: var(--status-pending); }

.stat-icon--preparing { background: var(--status-preparing-bg); }
.stat-icon--preparing svg { stroke: var(--status-preparing); }

.stat-icon--total     { background: rgba(99,1,22,0.07); }
.stat-icon--total svg { stroke: var(--accent-primary); }

.stat-info .stat-label {
  font-size: 12px; font-weight: 500;
  text-transform: uppercase; letter-spacing: 0.6px;
  color: var(--text-muted);
  margin-bottom: 2px;
}
.stat-info .stat-value {
  font-family: 'Crimson Pro', serif;
  font-size: 26px; font-weight: 600;
  color: var(--text-dark);
}

/* ═══════════════════════════════════════════
   ORDER CARDS GRID
   ═══════════════════════════════════════════ */
.orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}

/* ── Single Order Card ── */
.order-card {
  background: #fff;
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-rest);
  overflow: hidden;
  transition: box-shadow 0.3s ease, transform 0.3s ease;
  animation: fadeIn 0.45s ease-out backwards;
}
.order-card:hover {
  box-shadow: var(--shadow-hover);
  transform: translateY(-2px);
}

/* exit animation when completed / cancelled */
.order-card.exiting {
  animation: cardExit 0.4s ease-out forwards !important;
}
@keyframes cardExit {
  0%   { opacity: 1; transform: translateY(0) scale(1); }
  60%  { opacity: 0.4; transform: translateY(-8px) scale(0.97); }
  100% { opacity: 0; transform: translateY(-16px) scale(0.94); max-height: 0; padding: 0; margin: 0; }
}

/* ── Card Header ── */
.order-card__header {
  background: var(--accent-primary);
  padding: 14px 20px;
  display: flex; align-items: center; justify-content: space-between;
}
.order-card__header .order-id {
  font-family: 'Crimson Pro', serif;
  font-size: 18px; font-weight: 600;
  color: #fff;
}
.order-card__header .order-time {
  font-size: 12px; color: rgba(255,255,255,0.65);
  margin-top: 1px;
}

/* ── Status Badge (inside header) ── */
.status-badge {
  font-size: 12px; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.5px;
  padding: 4px 10px; border-radius: 12px;
  white-space: nowrap;
}
.status-badge--pending   { background: var(--status-pending-bg);   color: var(--status-pending); }
.status-badge--preparing { background: var(--status-preparing-bg); color: var(--status-preparing); }

/* ── Status Pipeline ── */
.status-pipeline {
  display: flex;
  align-items: center;
  padding: 14px 20px 0;
  gap: 0;
}
.pipeline-step {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 500;
  color: var(--bg-secondary);           /* inactive */
  transition: color 0.3s ease;
  white-space: nowrap;
}
.pipeline-step.active { color: var(--status-pending); }
.pipeline-step.active.preparing { color: var(--status-preparing); }
.pipeline-step.done   { color: var(--accent-success); }

.pipeline-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
  background: var(--bg-secondary);
  transition: background 0.3s ease, box-shadow 0.3s ease;
}
.pipeline-step.active .pipeline-dot {
  background: var(--status-pending);
  box-shadow: 0 0 0 3px var(--status-pending-bg);
}
.pipeline-step.active.preparing .pipeline-dot {
  background: var(--status-preparing);
  box-shadow: 0 0 0 3px var(--status-preparing-bg);
}
.pipeline-step.done .pipeline-dot {
  background: var(--accent-success);
  box-shadow: 0 0 0 3px var(--status-completed-bg);
}

.pipeline-line {
  height: 2px; flex: 1;
  background: var(--bg-secondary);
  transition: background 0.3s ease;
  min-width: 24px;
}
.pipeline-line.active { background: var(--status-preparing); }

/* ── Card Body ── */
.order-card__body { padding: 16px 20px; }

.order-customer {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 12px;
}
.order-customer svg {
  width: 16px; height: 16px;
  stroke: var(--text-muted); fill: none;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  flex-shrink: 0;
}
.order-customer span {
  font-size: 14px; font-weight: 500;
  color: var(--text-dark);
}

/* Items list */
.order-items { margin-bottom: 16px; }
.order-items .item-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 7px 0;
  border-bottom: 1px solid rgba(206,195,193,0.35);
  font-size: 14px;
}
.order-items .item-row:last-child { border-bottom: none; }
.order-items .item-name { color: var(--text-dark); font-weight: 500; }
.order-items .item-qty  {
  font-size: 13px; font-weight: 600;
  background: var(--bg-primary);
  color: var(--accent-primary);
  padding: 2px 8px; border-radius: 10px;
  border: 1px solid var(--bg-secondary);
}

/* ── Card Footer – Action Select ── */
.order-card__footer {
  padding: 0 20px 18px;
}
.action-label {
  font-size: 11px; font-weight: 500;
  text-transform: uppercase; letter-spacing: 0.6px;
  color: var(--text-muted);
  margin-bottom: 6px; display: block;
}

/* Custom styled select wrapper */
.select-wrap {
  position: relative;
}
.select-wrap::after {
  content: '';
  position: absolute; right: 14px; top: 50%;
  transform: translateY(-50%);
  width: 0; height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid var(--text-muted);
  pointer-events: none;
}
.status-select {
  width: 100%;
  padding: 11px 36px 11px 14px;
  font-size: 14px; font-weight: 500;
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-sm);
  color: var(--text-dark);
  cursor: pointer;
  appearance: none; -webkit-appearance: none;
  transition: all 0.2s ease;
}
.status-select:focus {
  border-color: var(--accent-secondary);
  box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
  outline: none;
}
.status-select:hover { border-color: var(--accent-secondary); }

/* ═══════════════════════════════════════════
   EMPTY STATE
   ═══════════════════════════════════════════ */
.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 72px 24px;
  animation: fadeIn 0.5s ease-out 0.2s backwards;
}
.empty-state .empty-icon {
  width: 64px; height: 64px;
  margin: 0 auto 20px;
  background: var(--bg-primary);
  border: 2px dashed var(--bg-secondary);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
}
.empty-state .empty-icon svg {
  width: 30px; height: 30px;
  stroke: var(--bg-secondary); fill: none;
  stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;
}
.empty-state h3 {
  font-family: 'Crimson Pro', serif;
  font-size: 20px; font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 6px;
}
.empty-state p {
  font-size: 14px; color: var(--text-muted);
  max-width: 340px; margin: 0 auto;
}

/* ═══════════════════════════════════════════
   TOAST NOTIFICATION
   ═══════════════════════════════════════════ */
.toast {
  position: fixed;
  bottom: 32px; right: 32px;
  z-index: 600;
  display: flex; align-items: center; gap: 12px;
  padding: 14px 20px;
  border-radius: var(--radius-md);
  font-size: 14px; font-weight: 500;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  color: #fff;
  transform: translateY(120%);
  opacity: 0;
  transition: all 0.35s cubic-bezier(0.34,1.56,0.64,1);
  pointer-events: none;
}
.toast.visible { transform: translateY(0); opacity: 1; }
.toast svg {
  width: 20px; height: 20px; flex-shrink: 0;
  fill: none; stroke: #fff;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.toast--success  { background: var(--accent-success); }
.toast--error    { background: var(--accent-primary); }

/* ─── Animations ─── */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 768px) {
  .content-wrapper { margin-left: 72px; padding: 32px 24px; }
  .page-header h1 { font-size: 32px; }
  .stats-bar { grid-template-columns: repeat(3, 1fr); gap: 12px; }
  .stat-card { padding: 16px 14px; gap: 12px; }
  .stat-info .stat-value { font-size: 22px; }
  .orders-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
  .content-wrapper { margin-left: 0; padding: 24px 16px; }
  .page-header h1 { font-size: 28px; }
  .stats-bar { grid-template-columns: repeat(3,1fr); gap: 8px; }
  .stat-card { padding: 14px 10px; gap: 10px; }
  .stat-icon { width: 38px; height: 38px; border-radius: 8px; }
  .stat-icon svg { width: 18px; height: 18px; }
  .stat-info .stat-label { font-size: 10px; }
  .stat-info .stat-value { font-size: 20px; }
  .toast { bottom: 20px; right: 16px; left: 16px; }
}
</style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
<main class="main-content">

  <!-- ── Page Header ── -->
  <div class="page-header">
    <h1>Kitchen Orders</h1>
    <p>Track and update order status in real-time</p>
  </div>

  <!-- ── Stats Bar ── -->
  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-icon stat-icon--pending">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="stat-info">
        <p class="stat-label">Pending</p>
        <p class="stat-value" id="stat-pending"><?= $countPending ?></p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon stat-icon--preparing">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </div>
      <div class="stat-info">
        <p class="stat-label">Preparing</p>
        <p class="stat-value" id="stat-preparing"><?= $countPreparing ?></p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon stat-icon--total">
        <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <div class="stat-info">
        <p class="stat-label">Total Active</p>
        <p class="stat-value" id="stat-total"><?= count($orders) ?></p>
      </div>
    </div>
  </div>

  <!-- ── Order Cards ── -->
  <div class="orders-grid" id="orders-grid">
    <?php if (empty($orders)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <h3>Kitchen is clear</h3>
        <p>No pending or in-progress orders right now. New orders will appear here automatically.</p>
      </div>
    <?php else: ?>
      <?php foreach ($orders as $i => $order):
        $isPending   = $order['status'] === 'Pending';
        $isPreparing = $order['status'] === 'Preparing';
      ?>
      <div class="order-card" data-order-id="<?= $order['order_id'] ?>" style="animation-delay:<?= $i * 0.07 ?>s;">

        <!-- Header -->
        <div class="order-card__header">
          <div>
            <span class="order-id">Order #<?= $order['order_id'] ?></span>
            <p class="order-time"><?= date('d M Y, H:i', strtotime($order['order_time'])) ?></p>
          </div>
          <span class="status-badge status-badge--<?= strtolower($order['status']) ?>">
            <?= $order['status'] ?>
          </span>
        </div>

        <!-- Status Pipeline -->
        <div class="status-pipeline">
          <div class="pipeline-step <?= $isPending ? 'active' : ($isPreparing ? 'done' : '') ?>">
            <div class="pipeline-dot"></div>
            <span>Pending</span>
          </div>
          <div class="pipeline-line <?= $isPreparing ? 'active' : '' ?>"></div>
          <div class="pipeline-step <?= $isPreparing ? 'active preparing' : '' ?>">
            <div class="pipeline-dot"></div>
            <span>Preparing</span>
          </div>
          <div class="pipeline-line"></div>
          <div class="pipeline-step">
            <div class="pipeline-dot"></div>
            <span>Done</span>
          </div>
        </div>

        <!-- Body -->
        <div class="order-card__body">
          <!-- Customer -->
          <div class="order-customer">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></span>
          </div>

          <!-- Items -->
          <div class="order-items">
            <?php foreach ($order['items'] as $item): ?>
              <div class="item-row">
                <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                <span class="item-qty">×<?= $item['quantity'] ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Footer – status selector -->
        <div class="order-card__footer">
          <span class="action-label">Update Status</span>
          <div class="select-wrap">
            <select class="status-select">
              <?php
                $statuses = ['Pending', 'Preparing', 'Completed', 'Cancelled'];
                foreach ($statuses as $s):
                  $sel = ($s === $order['status']) ? 'selected' : '';
              ?>
                <option value="<?= $s ?>" <?= $sel ?><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

      </div><!-- /order-card -->
      <?php endforeach; ?>
    <?php endif; ?>
  </div><!-- /orders-grid -->

</main>
</div>

<!-- ── Toast ── -->
<div class="toast" id="toast">
  <svg id="toast-icon" viewBox="0 0 24 24"></svg>
  <span id="toast-msg"></span>
</div>

<!-- ─── JavaScript ─── -->
<script>
(function () {
  /* ── Toast helper ── */
  const toast    = document.getElementById('toast');
  const toastMsg = document.getElementById('toast-msg');
  const toastIcon= document.getElementById('toast-icon');
  let   toastTimer = null;

  const SVG_CHECK = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';
  const SVG_ERR   = '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>';

  function showToast(msg, type) {  // type: 'success' | 'error'
    toastMsg.textContent = msg;
    toastIcon.innerHTML  = type === 'success' ? SVG_CHECK : SVG_ERR;
    toast.className      = 'toast toast--' + type + ' visible';
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('visible'), 3200);
  }

  /* ── Live stat counters (client-side) ── */
  const statPending   = document.getElementById('stat-pending');
  const statPreparing = document.getElementById('stat-preparing');
  const statTotal     = document.getElementById('stat-total');

  function updateStats() {
    const cards     = document.querySelectorAll('.order-card:not(.exiting)');
    let   pending   = 0, preparing = 0;
    cards.forEach(card => {
      const sel = card.querySelector('.status-select');
      if (!sel) return;
      if (sel.value === 'Pending')   pending++;
      if (sel.value === 'Preparing') preparing++;
    });
    statPending.textContent   = pending;
    statPreparing.textContent = preparing;
    statTotal.textContent     = cards.length;
  }

  /* ── Pipeline updater (visual only, before AJAX confirms) ── */
  function updatePipeline(card, newStatus) {
    const steps = card.querySelectorAll('.pipeline-step');
    const lines = card.querySelectorAll('.pipeline-line');
    // steps: 0 = Pending, 1 = Preparing, 2 = Done
    // lines: 0 = between Pending & Preparing, 1 = between Preparing & Done

    steps.forEach(s => s.classList.remove('active', 'preparing', 'done'));
    lines.forEach(l => l.classList.remove('active'));

    if (newStatus === 'Pending') {
      steps[0].classList.add('active');
    } else if (newStatus === 'Preparing') {
      steps[0].classList.add('done');
      lines[0].classList.add('active');
      steps[1].classList.add('active', 'preparing');
    } else {
      // Completed / Cancelled – all done
      steps[0].classList.add('done');
      lines[0].classList.add('active');
      steps[1].classList.add('done');
      lines[1].classList.add('active');
      steps[2].classList.add('done');
    }

    // Badge update
    const badge = card.querySelector('.status-badge');
    badge.className = 'status-badge status-badge--' + newStatus.toLowerCase();
    badge.textContent = newStatus;
  }

  /* ── Attach change listeners ── */
  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', () => {
      const card    = select.closest('.order-card');
      const orderId = card.dataset.orderId;
      const status  = select.value;

      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'order_id=' + orderId + '&status=' + encodeURIComponent(status)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          updatePipeline(card, status);
          updateStats();

          if (status === 'Completed' || status === 'Cancelled') {
            showToast('Order #' + orderId + ' marked as ' + status, 'success');
            card.classList.add('exiting');
            card.addEventListener('animationend', () => {
              card.remove();
              updateStats();

              // Show empty state if grid is now empty
              const grid = document.getElementById('orders-grid');
              if (grid.querySelectorAll('.order-card').length === 0 && !grid.querySelector('.empty-state')) {
                grid.innerHTML =
                  '<div class="empty-state">' +
                  '<div class="empty-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>' +
                  '<h3>Kitchen is clear</h3>' +
                  '<p>No pending or in-progress orders right now. New orders will appear here automatically.</p>' +
                  '</div>';
              }
            });
          } else {
            showToast('Order #' + orderId + ' → ' + status, 'success');
          }
        } else {
          // revert select visually
          showToast('Failed: ' + (data.msg || 'unknown error'), 'error');
        }
      })
      .catch(() => showToast('Network error — please retry', 'error'));
    });
  });

})();
</script>
</body>
</html>
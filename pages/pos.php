<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch active menu items
$menuData = [];
$result   = $conn->query("SELECT id, name, price FROM items WHERE is_active = 1 ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $menuData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Point of Sale</title>
<link rel="stylesheet" href="../assets/css/style.css">
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
.page-header { margin-bottom: 32px; animation: fadeIn 0.5s ease-out; }
.page-header h1 {
  font-family: 'Crimson Pro', serif;
  font-size: 38px; font-weight: 600;
  color: var(--accent-primary);
  letter-spacing: -0.5px; margin-bottom: 6px;
}
.page-header p { font-size: 15px; color: var(--text-muted); }

/* ═══════════════════════════════════════════
   TWO-PANEL GRID
   ═══════════════════════════════════════════ */
.pos-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 32px;
  align-items: start;
}

/* ─── LEFT: Menu ─── */
.menu-panel { animation: fadeIn 0.5s ease-out 0.1s backwards; }

.panel-label {
  font-size: 12px; font-weight: 500;
  text-transform: uppercase; letter-spacing: 0.8px;
  color: var(--text-muted); margin-bottom: 14px;
}

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
}

/* ── Item Card ── */
.item-card {
  background: #fff;
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-md);
  padding: 24px 18px;
  text-align: center;
  cursor: pointer;
  box-shadow: var(--shadow-rest);
  transition: all 0.2s ease;
  user-select: none;
  -webkit-user-select: none;
  position: relative;
  overflow: hidden;
}
.item-card::before {
  content: '';
  position: absolute; inset: 0;
  background: var(--accent-primary);
  opacity: 0;
  transition: opacity 0.2s ease;
  z-index: 0;
}
.item-card:hover {
  border-color: var(--accent-secondary);
  box-shadow: var(--shadow-hover);
  transform: translateY(-3px);
}
.item-card:hover::before { opacity: 0.04; }
.item-card:active { transform: translateY(0) scale(0.96); box-shadow: var(--shadow-rest); }

.item-card h4,
.item-card p { position: relative; z-index: 1; }

.item-card h4 {
  font-family: 'Crimson Pro', serif;
  font-size: 17px; font-weight: 600;
  color: var(--accent-primary);
  margin-bottom: 8px;
  line-height: 1.25;
}
.item-card p {
  font-size: 15px; font-weight: 600;
  color: var(--accent-secondary);
}

/* ── "+" badge appears on hover ── */
.item-card .add-badge {
  position: absolute; top: 10px; right: 10px;
  width: 26px; height: 26px;
  background: var(--accent-primary);
  color: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 600;
  opacity: 0;
  transform: scale(0.7);
  transition: all 0.2s ease;
  z-index: 1;
}
.item-card:hover .add-badge { opacity: 1; transform: scale(1); }

/* ─── RIGHT: Order Panel (sticky) ─── */
.order-panel {
  background: #fff;
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-rest);
  overflow: hidden;
  position: sticky;
  top: 48px;
  animation: fadeIn 0.5s ease-out 0.15s backwards;
  transition: box-shadow 0.3s ease;
}
.order-panel:hover { box-shadow: var(--shadow-hover); }

/* Panel header bar */
.order-panel__header {
  background: var(--accent-primary);
  padding: 18px 22px;
  display: flex; align-items: center; justify-content: space-between;
}
.order-panel__header h3 {
  font-family: 'Crimson Pro', serif;
  font-size: 20px; font-weight: 600;
  color: #fff; letter-spacing: -0.2px;
}
.order-panel__header .item-count {
  font-size: 13px; font-weight: 500;
  background: rgba(255,255,255,0.18);
  color: #fff;
  padding: 3px 10px; border-radius: 12px;
}

/* Scrollable body */
.order-panel__body {
  padding: 0;
  max-height: 420px;
  overflow-y: auto;
}
/* thin scrollbar */
.order-panel__body::-webkit-scrollbar { width: 6px; }
.order-panel__body::-webkit-scrollbar-track { background: transparent; }
.order-panel__body::-webkit-scrollbar-thumb { background: var(--bg-secondary); border-radius: 3px; }

/* Order row */
.order-row {
  display: grid;
  grid-template-columns: 1fr auto auto auto;
  align-items: center;
  gap: 12px;
  padding: 13px 22px;
  border-bottom: 1px solid rgba(206,195,193,0.45);
  transition: background 0.15s ease;
}
.order-row:last-child { border-bottom: none; }
.order-row:hover { background: rgba(251,249,245,0.7); }

.order-row .row-name {
  font-size: 14px; font-weight: 500; color: var(--text-dark);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.order-row .row-qty {
  font-size: 14px; font-weight: 600; color: var(--accent-primary);
  text-align: center; min-width: 24px;
}
.order-row .row-sub {
  font-size: 14px; font-weight: 600; color: var(--text-dark);
  text-align: right; min-width: 56px;
}
.order-row .row-remove {
  width: 28px; height: 28px;
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-sm);
  background: var(--bg-primary);
  color: var(--accent-primary);
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s ease;
  font-size: 16px; line-height: 1;
}
.order-row .row-remove:hover {
  background: var(--accent-primary);
  color: #fff;
  border-color: var(--accent-primary);
}

/* Empty order placeholder */
.order-empty {
  padding: 44px 22px;
  text-align: center;
  color: var(--text-muted);
  font-size: 14px;
}
.order-empty svg {
  width: 36px; height: 36px;
  stroke: var(--bg-secondary); fill: none;
  stroke-width: 1.5; margin-bottom: 10px;
  stroke-linecap: round; stroke-linejoin: round;
}

/* Panel footer – total + button */
.order-panel__footer {
  padding: 18px 22px 22px;
  border-top: 1px solid var(--bg-secondary);
}
.total-row {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px;
}
.total-row .total-label {
  font-size: 13px; font-weight: 500;
  text-transform: uppercase; letter-spacing: 0.6px;
  color: var(--text-muted);
}
.total-row .total-value {
  font-family: 'Crimson Pro', serif;
  font-size: 26px; font-weight: 600;
  color: var(--accent-primary);
}

.btn-finalize {
  width: 100%;
  padding: 15px;
  font-size: 14px; font-weight: 600;
  font-family: 'Work Sans', sans-serif;
  text-transform: uppercase; letter-spacing: 0.5px;
  background: var(--accent-primary);
  color: #fff; border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-finalize:hover {
  background: #4a010f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99,1,22,0.25);
}
.btn-finalize:active { transform: translateY(0); box-shadow: none; }
.btn-finalize:disabled {
  opacity: 0.45; cursor: not-allowed;
  transform: none; box-shadow: none;
}

/* ═══════════════════════════════════════════
   MODAL OVERLAY
   ═══════════════════════════════════════════ */
.modal-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(30,20,22,0.52);
  backdrop-filter: blur(3px);
  z-index: 500;
  align-items: center; justify-content: center;
  padding: 24px;
  animation: fadeOverlay 0.2s ease-out;
}
.modal-overlay.active { display: flex; }

@keyframes fadeOverlay {
  from { opacity: 0; }
  to   { opacity: 1; }
}

.modal {
  background: #fff;
  border-radius: 12px;
  width: 100%; max-width: 480px;
  box-shadow: 0 24px 48px rgba(99,1,22,0.18);
  overflow: hidden;
  animation: modalSlide 0.25s ease-out;
}
@keyframes modalSlide {
  from { opacity: 0; transform: translateY(24px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Modal header */
.modal__header {
  background: var(--accent-primary);
  padding: 22px 28px;
}
.modal__header h3 {
  font-family: 'Crimson Pro', serif;
  font-size: 22px; font-weight: 600;
  color: #fff; letter-spacing: -0.2px;
}
.modal__header p {
  font-size: 13px; color: rgba(255,255,255,0.7);
  margin-top: 2px;
}

/* Modal body – scrollable on short screens */
.modal__body {
  padding: 28px;
  max-height: 60vh;
  overflow-y: auto;
}

/* Section divider inside modal */
.modal-section-title {
  font-size: 12px; font-weight: 500;
  text-transform: uppercase; letter-spacing: 0.7px;
  color: var(--text-muted);
  margin-bottom: 12px;
  margin-top: 24px;
}
.modal-section-title:first-child { margin-top: 0; }

/* Modal inputs */
.modal-input {
  width: 100%;
  padding: 13px 16px;
  font-size: 15px;
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-sm);
  color: var(--text-dark);
  transition: all 0.2s ease;
  margin-bottom: 12px;
  appearance: none; -webkit-appearance: none;
}
.modal-input::placeholder { color: #999; }
.modal-input:focus {
  border-color: var(--accent-secondary);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
  outline: none;
}
/* select arrow */
.select-wrap { position: relative; }
.select-wrap::after {
  content: '';
  position: absolute; right: 16px; top: 50%;
  transform: translateY(-50%);
  width: 0; height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid var(--text-muted);
  pointer-events: none;
}
.select-wrap .modal-input { padding-right: 36px; }

/* Change row */
.change-row {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--bg-primary);
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-sm);
  padding: 12px 16px;
  margin-top: 4px;
}
.change-row span { font-size: 14px; color: var(--text-muted); font-weight: 500; }
.change-row strong {
  font-size: 18px;
  font-family: 'Crimson Pro', serif;
  color: var(--accent-success);
}

/* Inline error inside modal */
.modal-error {
  display: none;
  background: #fff5f5;
  border: 1px solid var(--accent-primary);
  color: var(--accent-primary);
  border-radius: var(--radius-sm);
  padding: 11px 14px;
  font-size: 13px;
  margin-bottom: 12px;
  animation: shake 0.3s ease-out;
}
.modal-error.visible { display: block; }

/* Modal footer buttons */
.modal__footer {
  padding: 0 28px 24px;
  display: flex; gap: 12px;
}
.btn-modal-primary {
  flex: 1;
  padding: 14px;
  font-size: 14px; font-weight: 600;
  font-family: 'Work Sans', sans-serif;
  text-transform: uppercase; letter-spacing: 0.5px;
  background: var(--accent-primary);
  color: #fff; border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-modal-primary:hover {
  background: #4a010f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99,1,22,0.25);
}
.btn-modal-primary:active { transform: translateY(0); box-shadow: none; }

.btn-modal-secondary {
  flex: 0 0 auto;
  padding: 14px 22px;
  font-size: 14px; font-weight: 500;
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  color: var(--text-muted);
  border: 1px solid var(--bg-secondary);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-modal-secondary:hover {
  background: var(--bg-secondary);
  color: var(--text-dark);
}

/* ── Confirmed State ── */
.confirmed-state {
  display: none;
  text-align: center;
  padding: 44px 28px 36px;
}
.confirmed-state.visible { display: block; }

.confirmed-icon {
  width: 72px; height: 72px;
  background: #f0f4e8;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.confirmed-icon svg {
  width: 36px; height: 36px;
  stroke: var(--accent-success); fill: none;
  stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
.confirmed-state h3 {
  font-family: 'Crimson Pro', serif;
  font-size: 24px; font-weight: 600;
  color: var(--accent-success);
  margin-bottom: 6px;
}
.confirmed-state p {
  font-size: 14px; color: var(--text-muted);
  margin-bottom: 24px;
}
.confirmed-btns { display: flex; gap: 12px; justify-content: center; }
.confirmed-btns .btn-modal-primary { flex: 0 0 auto; padding: 12px 24px; }
.confirmed-btns .btn-modal-secondary { flex: 0 0 auto; padding: 12px 20px; }

/* ─── Animations ─── */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes shake {
  0%,100% { transform: translateX(0); }
  25%     { transform: translateX(-8px); }
  75%     { transform: translateX(8px); }
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 960px) {
  .pos-grid { grid-template-columns: 1fr; }
  .order-panel { position: static; }
  .order-panel__body { max-height: 280px; }
}
@media (max-width: 768px) {
  .content-wrapper { margin-left: 72px; padding: 32px 24px; }
  .page-header h1 { font-size: 32px; }
  .menu-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; }
}
@media (max-width: 480px) {
  .content-wrapper { margin-left: 0; padding: 24px 16px; }
  .page-header h1 { font-size: 28px; }
  .menu-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .item-card { padding: 18px 12px; }
  .pos-grid { gap: 20px; }
}
</style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
<main class="main-content">

  <!-- ── Page Header ── -->
  <header class="page-header">
    <h1>Point of Sale</h1>
    <p>Select items to build the current order</p>
  </header>

  <!-- ── Two-Panel Grid ── -->
  <div class="pos-grid">

    <!-- ── LEFT: Menu Grid ── -->
    <div class="menu-panel">
      <p class="panel-label">Menu</p>
      <div class="menu-grid" id="menu-grid">
        <!-- populated by JS -->
      </div>
    </div>

    <!-- ── RIGHT: Live Order Panel ── -->
    <div class="order-panel">
      <div class="order-panel__header">
        <h3>Current Order</h3>
        <span class="item-count" id="item-count">0 items</span>
      </div>

      <div class="order-panel__body" id="order-body">
        <div class="order-empty" id="order-empty">
          <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          No items added yet
        </div>
        <!-- order rows injected here -->
      </div>

      <div class="order-panel__footer">
        <div class="total-row">
          <span class="total-label">Total</span>
          <span class="total-value">₹<span id="total">0.00</span></span>
        </div>
        <button class="btn-finalize" id="finalize-btn" disabled>Finalize Order</button>
      </div>
    </div>

  </div><!-- /pos-grid -->
</main>
</div>

<!-- ═══════════════════════════════════════════
     MODAL
     ═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-overlay">
  <div class="modal">

    <!-- Form state -->
    <div id="modal-form-state">
      <div class="modal__header">
        <h3>Finalize Order</h3>
        <p>Customer details &amp; payment</p>
      </div>

      <div class="modal__body">
        <!-- inline error -->
        <div class="modal-error" id="modal-error"></div>

        <!-- Customer -->
        <p class="modal-section-title">Customer</p>
        <input type="text"   id="cust-name"   class="modal-input" placeholder="Name"   value="Guest">
        <input type="email"  id="cust-email"  class="modal-input" placeholder="Email (optional)">
        <input type="text"   id="cust-mobile" class="modal-input" placeholder="Mobile (optional)">

        <!-- Payment -->
        <p class="modal-section-title">Payment</p>
        <div class="select-wrap">
          <select id="payment-mode" class="modal-input">
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="UPI">UPI</option>
          </select>
        </div>
        <input type="number" id="amount-taken" class="modal-input" placeholder="Amount given" min="0" step="0.01">

        <div class="change-row">
          <span>Change to give</span>
          <strong>₹<span id="change">0.00</span></strong>
        </div>
      </div>

      <div class="modal__footer">
        <button class="btn-modal-primary"    id="submit-order-btn">Submit Order</button>
        <button class="btn-modal-secondary"  id="cancel-order-btn">Cancel</button>
      </div>
    </div>

    <!-- Confirmed state (hidden initially) -->
    <div class="confirmed-state" id="confirmed-state">
      <div class="confirmed-icon">
        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <h3>Order Confirmed</h3>
      <p>The order has been saved successfully.</p>
      <div class="confirmed-btns">
        <button class="btn-modal-primary"   id="print-invoice-btn">Print Invoice</button>
        <button class="btn-modal-secondary" id="close-order-btn">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- ─── JavaScript ─── -->
<script>
(function () {
  /* ── DOM refs ── */
  const menuGrid      = document.getElementById('menu-grid');
  const orderBody     = document.getElementById('order-body');
  const orderEmpty    = document.getElementById('order-empty');
  const totalEl       = document.getElementById('total');
  const itemCountEl   = document.getElementById('item-count');
  const finalizeBtn   = document.getElementById('finalize-btn');

  const overlay       = document.getElementById('modal-overlay');
  const modalError    = document.getElementById('modal-error');
  const custNameEl    = document.getElementById('cust-name');
  const custEmailEl   = document.getElementById('cust-email');
  const custMobileEl  = document.getElementById('cust-mobile');
  const paymentModeEl = document.getElementById('payment-mode');
  const amountTakenEl = document.getElementById('amount-taken');
  const changeEl      = document.getElementById('change');
  const submitBtn     = document.getElementById('submit-order-btn');
  const cancelBtn     = document.getElementById('cancel-order-btn');

  const formState     = document.getElementById('modal-form-state');
  const confirmedState= document.getElementById('confirmed-state');

  /* ── Data ── */
  const menuData = <?php echo json_encode($menuData); ?>;
  let   order    = [];
  let   lastOrderId = null;

  /* ════════════════════════════════════════
     MENU GRID
     ════════════════════════════════════════ */
  function renderMenuGrid() {
    menuGrid.innerHTML = '';
    menuData.forEach((item, i) => {
      const card = document.createElement('div');
      card.className = 'item-card';
      card.style.animationDelay = (i * 0.04) + 's';
      card.style.animation = 'fadeIn 0.4s ease-out backwards';
      card.innerHTML =
        '<span class="add-badge">+</span>' +
        '<h4>' + item.name + '</h4>' +
        '<p>₹' + Number(item.price).toFixed(2) + '</p>';
      card.addEventListener('click', () => addToOrder(item));
      menuGrid.appendChild(card);
    });
  }

  /* ════════════════════════════════════════
     ORDER LOGIC
     ════════════════════════════════════════ */
  function addToOrder(item) {
    const existing = order.find(o => o.id === item.id);
    if (existing) existing.qty += 1;
    else          order.push({ id: item.id, name: item.name, price: item.price, qty: 1 });
    renderOrder();
  }

  function removeFromOrder(index) {
    order.splice(index, 1);
    renderOrder();
  }

  function renderOrder() {
    /* clear rows (keep empty placeholder) */
    const rows = orderBody.querySelectorAll('.order-row');
    rows.forEach(r => r.remove());

    let total    = 0;
    let totalQty = 0;

    order.forEach((item, i) => {
      const subtotal = item.qty * item.price;
      total    += subtotal;
      totalQty += item.qty;

      const row = document.createElement('div');
      row.className = 'order-row';
      row.innerHTML =
        '<span class="row-name">' + item.name + '</span>' +
        '<span class="row-qty">×' + item.qty + '</span>' +
        '<span class="row-sub">₹' + subtotal.toFixed(2) + '</span>' +
        '<button class="row-remove" data-index="' + i + '">×</button>';
      row.querySelector('.row-remove').addEventListener('click', () => removeFromOrder(i));
      orderBody.appendChild(row);
    });

    /* toggle empty placeholder */
    orderEmpty.style.display = order.length === 0 ? 'block' : 'none';

    /* update totals */
    totalEl.textContent    = total.toFixed(2);
    itemCountEl.textContent = totalQty + ' item' + (totalQty !== 1 ? 's' : '');
    finalizeBtn.disabled   = order.length === 0;

    /* sync change display */
    updateChange();
  }

  /* ════════════════════════════════════════
     MODAL – OPEN / CLOSE
     ════════════════════════════════════════ */
  finalizeBtn.addEventListener('click', () => {
    /* reset form */
    custNameEl.value    = 'Guest';
    custEmailEl.value   = '';
    custMobileEl.value  = '';
    paymentModeEl.value = 'Cash';
    amountTakenEl.value = '';
    changeEl.textContent = '0.00';
    modalError.textContent = '';
    modalError.classList.remove('visible');

    /* ensure form visible, confirmed hidden */
    formState.style.display      = 'block';
    confirmedState.classList.remove('visible');

    overlay.classList.add('active');
  });

  function closeModal() {
    overlay.classList.remove('active');
  }
  cancelBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

  /* ════════════════════════════════════════
     CHANGE CALC
     ════════════════════════════════════════ */
  function updateChange() {
    const total = parseFloat(totalEl.textContent) || 0;
    const taken = parseFloat(amountTakenEl.value)  || 0;
    const diff  = taken - total;
    changeEl.textContent = diff >= 0 ? diff.toFixed(2) : '0.00';
  }
  amountTakenEl.addEventListener('input', updateChange);

  /* ════════════════════════════════════════
     SUBMIT ORDER
     ════════════════════════════════════════ */
  submitBtn.addEventListener('click', () => {
    const name        = custNameEl.value.trim() || 'Guest';
    const email       = custEmailEl.value.trim();
    const mobile      = custMobileEl.value.trim();
    const paymentMode = paymentModeEl.value;
    const amountTaken = parseFloat(amountTakenEl.value) || 0;
    const totalAmount = parseFloat(totalEl.textContent);
    const change      = Math.max(0, amountTaken - totalAmount);

    /* ── validation ── */
    if (amountTaken < totalAmount) {
      modalError.textContent = 'Amount given (₹' + amountTaken.toFixed(2) + ') is less than the total (₹' + totalAmount.toFixed(2) + ').';
      modalError.classList.add('visible');
      /* re-trigger shake */
      modalError.style.animation = 'none';
      void modalError.offsetWidth;            /* force reflow */
      modalError.style.animation = 'shake 0.3s ease-out';
      return;
    }
    modalError.classList.remove('visible');

    /* ── POST ── */
    fetch('../handlers/save_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        order: order,
        customer: { name: name, email: email, mobile: mobile },
        payment: { mode: paymentMode, taken: amountTaken, change: change }
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        lastOrderId = data.order_id;

        /* swap to confirmed view */
        formState.style.display = 'none';
        confirmedState.classList.add('visible');

        /* reset order */
        order = [];
        renderOrder();
      } else {
        modalError.textContent = 'Failed to save: ' + (data.error || 'unknown error');
        modalError.classList.add('visible');
      }
    })
    .catch(() => {
      modalError.textContent = 'Network error. Please try again.';
      modalError.classList.add('visible');
    });
  });

  /* ── Print & Close (confirmed state) ── */
  document.getElementById('print-invoice-btn').addEventListener('click', () => {
    if (lastOrderId) window.open('../handlers/invoice.php?order_id=' + lastOrderId, '_blank');
  });
  document.getElementById('close-order-btn').addEventListener('click', closeModal);

  /* ── Init ── */
  renderMenuGrid();
  renderOrder();
})();
</script>
</body>
</html>
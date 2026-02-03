<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

$current_page = basename($_SERVER['PHP_SELF']);
$order_items  = [];
$order_found  = false;
$order_id     = '';
$success_msg  = '';
$error_msg    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── Step 1: Look-up order items ── */
    if (isset($_POST['order_id_submit'])) {
        $order_id = intval($_POST['order_id']);

        $stmt = $conn->prepare("
            SELECT oi.id AS order_item_id, i.name AS item_name
            FROM order_items oi
            JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_found = true;
            while ($row = $result->fetch_assoc()) {
                $order_items[] = $row;
            }
        } else {
            $error_msg = "No items found for this Order ID.";
        }
    }

    /* ── Step 2: Persist feedback ── */
    if (isset($_POST['feedback_submit'])) {
        $order_id = intval($_POST['order_id']);

        if (isset($_POST['rating']) && is_array($_POST['rating'])) {
            foreach ($_POST['rating'] as $item_name => $rating) {
                $comment = $_POST['comment'][$item_name] ?? '';

                $stmt = $conn->prepare("SELECT id FROM items WHERE name = ? LIMIT 1");
                $stmt->bind_param("s", $item_name);
                $stmt->execute();
                $item_res = $stmt->get_result();

                if ($item_res->num_rows > 0) {
                    $item_id    = $item_res->fetch_assoc()['id'];
                    $customer_id = $_SESSION['customer_id'] ?? 1; // fallback

                    $stmt2 = $conn->prepare("INSERT INTO feedback (customer_id, item_id, rating, comment) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("iiis", $customer_id, $item_id, $rating, $comment);
                    $stmt2->execute();
                }
            }
            $success_msg = "Feedback submitted successfully!";
        } else {
            $error_msg = "Please rate at least one item before submitting.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Feedback – Tablet</title>
<link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  /* ─── CSS Variables ─── */
  :root {
    --bg-primary:        #FBF9F5;
    --bg-secondary:      #CEC3C1;
    --accent-primary:    #630116;
    --accent-secondary:  #AF5B73;
    --accent-success:    #7E892B;
    --text-dark:         #2a2a2a;
    --text-muted:        #6a6a6a;
    --shadow-rest:       0 2px 8px rgba(99,1,22,0.04);
    --shadow-hover:      0 8px 24px rgba(99,1,22,0.12);
    --radius-sm:         6px;
    --radius-md:         8px;
  }

  /* ─── Reset ─── */
  *, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Work Sans', sans-serif;
    background: var(--bg-primary);
    color: var(--text-dark);
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 48px 24px;
  }

  /* ─── Page Shell (no sidebar – centred) ─── */
  .page-shell {
    width: 100%;
    max-width: 680px;
    animation: fadeIn 0.5s ease-out;
  }

  /* ─── Page Header ─── */
  .page-header {
    text-align: center;
    margin-bottom: 40px;
  }
  .page-header h1 {
    font-family: 'Crimson Pro', serif;
    font-size: 38px;
    font-weight: 600;
    color: var(--accent-primary);
    letter-spacing: -0.5px;
    margin-bottom: 6px;
  }
  .page-header p {
    font-size: 15px;
    color: var(--text-muted);
  }

  /* ─── Alerts ─── */
  .alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    margin-bottom: 24px;
  }
  .alert svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .alert--error {
    background: #fff5f5;
    border: 1px solid var(--accent-primary);
    color: var(--accent-primary);
    animation: shake 0.3s ease-out;
  }
  .alert--error svg { stroke: var(--accent-primary); }

  .alert--success {
    background: #f0f4e8;
    border: 1px solid var(--accent-success);
    color: var(--accent-success);
    animation: slideIn 0.3s ease-out;
  }
  .alert--success svg { stroke: var(--accent-success); }

  /* ─── Input Card (Order ID entry) ─── */
  .input-card {
    background: #fff;
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 40px 36px;
    box-shadow: var(--shadow-rest);
    animation: fadeIn 0.5s ease-out 0.1s backwards;
  }

  .input-card label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--text-dark);
    margin-bottom: 8px;
  }

  .input-card input[type="number"] {
    width: 100%;
    padding: 14px 16px;
    font-size: 16px;
    font-family: 'Work Sans', sans-serif;
    background: var(--bg-primary);
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-sm);
    color: var(--text-dark);
    transition: all 0.2s ease;
    margin-bottom: 20px;
  }
  .input-card input[type="number"]::placeholder {
    color: #999;
  }
  .input-card input[type="number"]:focus {
    border-color: var(--accent-secondary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
    outline: none;
  }

  /* ─── Primary Button ─── */
  .btn-primary {
    width: 100%;
    padding: 16px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Work Sans', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--accent-primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .btn-primary:hover {
    background: #4a010f;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99,1,22,0.25);
  }
  .btn-primary:active {
    transform: translateY(0);
    box-shadow: none;
  }

  /* ─── Feedback Cards (one per item) ─── */
  .feedback-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 28px;
  }

  .feedback-card {
    background: #fff;
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 28px 32px;
    box-shadow: var(--shadow-rest);
    transition: box-shadow 0.3s ease;
    animation: fadeIn 0.4s ease-out backwards;
  }
  .feedback-card:hover {
    box-shadow: var(--shadow-hover);
  }

  .feedback-card h3 {
    font-family: 'Crimson Pro', serif;
    font-size: 22px;
    font-weight: 600;
    color: var(--accent-primary);
    letter-spacing: -0.3px;
    margin-bottom: 16px;
  }

  /* ─── Star Rating ─── */
  .star-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--text-dark);
    margin-bottom: 8px;
  }

  .stars {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
  }
  .stars span {
    font-size: 32px;
    color: var(--bg-secondary);
    cursor: pointer;
    transition: color 0.15s ease, transform 0.15s ease;
    user-select: none;
    -webkit-user-select: none;
  }
  .stars span.hover,
  .stars span.selected {
    color: #d4a017;
  }
  .stars span:hover {
    transform: scale(1.15);
  }

  /* ─── Textarea ─── */
  .feedback-card textarea {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    font-family: 'Work Sans', sans-serif;
    background: var(--bg-primary);
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-sm);
    color: var(--text-dark);
    resize: vertical;
    min-height: 100px;
    transition: all 0.2s ease;
  }
  .feedback-card textarea::placeholder {
    color: #999;
  }
  .feedback-card textarea:focus {
    border-color: var(--accent-secondary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
    outline: none;
  }

  /* ─── Back Link ─── */
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: var(--accent-secondary);
    text-decoration: none;
    margin-bottom: 28px;
    transition: color 0.2s ease;
  }
  .back-link:hover { color: var(--accent-primary); }
  .back-link svg {
    width: 16px; height: 16px;
    stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }

  /* ─── Animations ─── */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0);    }
  }
  @keyframes shake {
    0%,100% { transform: translateX(0);   }
    25%     { transform: translateX(-8px); }
    75%     { transform: translateX(8px);  }
  }
  @keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0);     }
  }

  /* ═══════════════════════════════════════════
     RESPONSIVE
     ═══════════════════════════════════════════ */
  @media (max-width: 540px) {
    body          { padding: 32px 16px; }
    .page-header h1 { font-size: 30px; }
    .input-card   { padding: 28px 24px; }
    .feedback-card { padding: 24px 20px; }
  }
</style>
</head>
<body>

<div class="page-shell">

  <!-- ── Header ── -->
  <div class="page-header">
    <h1>Customer Feedback</h1>
    <p>Submit ratings and comments for your order</p>
  </div>

  <!-- ── Success Alert (shown after submission, auto-redirects) ── -->
  <?php if (!empty($success_msg)): ?>
    <div class="alert alert--success">
      <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <?= htmlspecialchars($success_msg) ?>
    </div>
    <script>
      setTimeout(function(){ window.location.href = '<?= basename($_SERVER['PHP_SELF']) ?>'; }, 2500);
    </script>
  <?php endif; ?>

  <!-- ── Error Alert ── -->
  <?php if (!empty($error_msg)): ?>
    <div class="alert alert--error">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error_msg) ?>
    </div>
  <?php endif; ?>

  <!-- ── Step 1 – Enter Order ID ── -->
  <?php if (!$order_found && empty($success_msg)): ?>
    <div class="input-card">
      <form method="POST">
        <label for="order_id">Order ID</label>
        <input type="number" id="order_id" name="order_id"
               placeholder="e.g. 1042"
               value="<?= htmlspecialchars($order_id) ?>"
               required
               autocomplete="off">
        <button type="submit" name="order_id_submit" class="btn-primary">Check Order</button>
      </form>
    </div>
  <?php endif; ?>

  <!-- ── Step 2 – Rate &amp; Comment ── -->
  <?php if ($order_found): ?>
    <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="20" y1="12" x2="4" y2="12"/><polyline points="10 18 4 12 10 6"/></svg>
      Back
    </a>

    <form method="POST">
      <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

      <div class="feedback-list">
        <?php foreach ($order_items as $i => $item): ?>
          <div class="feedback-card" style="animation-delay: <?= $i * 0.08 ?>s;">
            <h3><?= htmlspecialchars($item['item_name']) ?></h3>

            <!-- Star Rating -->
            <span class="star-label">Your Rating</span>
            <div class="stars" data-item="<?= htmlspecialchars($item['item_name']) ?>">
              <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>

            <!-- Comment -->
            <textarea name="comment[<?= htmlspecialchars($item['item_name']) ?>]"
                      placeholder="Share your experience…"></textarea>
          </div>
        <?php endforeach; ?>
      </div>

      <button type="submit" name="feedback_submit" class="btn-primary">Submit Feedback</button>
    </form>
  <?php endif; ?>

</div><!-- /page-shell -->

<!-- ─── Star-Rating JS ─── -->
<script>
(function () {
  document.querySelectorAll('.stars').forEach(function (container) {
    var stars = container.querySelectorAll('span');
    var itemName = container.dataset.item;

    stars.forEach(function (star, index) {

      star.addEventListener('mouseover', function () {
        stars.forEach(function (s, i) { s.classList.toggle('hover', i <= index); });
      });

      star.addEventListener('mouseout', function () {
        stars.forEach(function (s) { s.classList.remove('hover'); });
      });

      star.addEventListener('click', function () {
        // Lock selection
        stars.forEach(function (s, i) { s.classList.toggle('selected', i <= index); });

        // Create or update hidden input
        var input = container.querySelector('input[type="hidden"]');
        if (!input) {
          input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'rating[' + itemName + ']';
          container.appendChild(input);
        }
        input.value = index + 1;
      });
    });
  });
})();
</script>
</body>
</html>
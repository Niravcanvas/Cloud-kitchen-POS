<?php
session_start();
include __DIR__ . '/../config/dbcon.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Feedback</title>
<link rel="stylesheet" href="static/style.css">
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
  }

  /* ─── Content Wrapper (pushes past sidebar) ─── */
  .content-wrapper {
    margin-left: 260px;
    min-height: 100vh;
    padding: 48px 40px;
    transition: margin-left 0.3s ease;
  }

  /* ─── Main Content Container ─── */
  .main-content {
    max-width: 1200px;
    margin: 0 auto;
  }

  /* ─── Page Header ─── */
  .page-header {
    margin-bottom: 40px;
    animation: fadeIn 0.5s ease-out;
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
    font-weight: 400;
  }

  /* ─── Summary Cards Row ─── */
  .summary-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
    margin-bottom: 32px;
    animation: fadeIn 0.5s ease-out 0.1s backwards;
  }

  .summary-cards .card {
    background: #fff;
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 32px;
    box-shadow: var(--shadow-rest);
    transition: box-shadow 0.3s ease, transform 0.3s ease;
  }
  .summary-cards .card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
  }

  .summary-cards .card h3 {
    font-family: 'Crimson Pro', serif;
    font-size: 22px;
    font-weight: 600;
    color: var(--accent-primary);
    letter-spacing: -0.3px;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--bg-secondary);
  }

  /* ─── Item Row inside summary card ─── */
  .item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(206,195,193,0.4);
  }
  .item-row:last-child { border-bottom: none; }

  .item-row .item-name {
    font-size: 15px;
    font-weight: 500;
    color: var(--text-dark);
  }
  .item-row .item-value {
    font-size: 15px;
    color: var(--text-muted);
    font-weight: 400;
  }

  /* ─── Stars (gold fill, muted unfilled) ─── */
  .stars-gold {
    color: #d4a017;
    letter-spacing: 2px;
  }
  .stars-gold .star-empty {
    color: var(--bg-secondary);
  }

  /* ─── Review Count Badge ─── */
  .review-badge {
    display: inline-block;
    background: var(--bg-primary);
    color: var(--accent-primary);
    font-size: 13px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid var(--bg-secondary);
  }

  /* ─── Tablet Button Row ─── */
  .tablet-btn-row {
    margin-bottom: 32px;
    animation: fadeIn 0.5s ease-out 0.2s backwards;
  }

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: var(--accent-primary);
    color: #fff;
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    border-radius: var(--radius-sm);
    cursor: pointer;
    text-decoration: none;
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
  .btn-primary svg {
    width: 18px;
    height: 18px;
    stroke: #fff;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* ─── Section Label ─── */
  .section-label {
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-muted);
    margin-bottom: 12px;
  }

  /* ─── Feedback Table Wrapper ─── */
  .table-wrapper {
    background: #fff;
    border: 1px solid var(--bg-secondary);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-rest);
    overflow: hidden;
    animation: fadeIn 0.5s ease-out 0.3s backwards;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  /* ─── Table Head ─── */
  thead {
    background: var(--accent-primary);
  }
  thead th {
    font-family: 'Work Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #fff;
    padding: 16px 20px;
    text-align: left;
    white-space: nowrap;
  }

  /* ─── Table Body ─── */
  tbody tr {
    border-bottom: 1px solid rgba(206,195,193,0.5);
    transition: background 0.2s ease;
  }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover {
    background: rgba(251,249,245,0.9);
  }

  tbody td {
    font-size: 14px;
    color: var(--text-dark);
    padding: 15px 20px;
    vertical-align: middle;
  }

  /* ID column – muted */
  tbody td.col-id {
    color: var(--text-muted);
    font-weight: 500;
    font-size: 13px;
  }

  /* Date column – muted */
  tbody td.col-date {
    color: var(--text-muted);
    font-size: 13px;
    white-space: nowrap;
  }

  /* Name & Item columns */
  tbody td.col-name,
  tbody td.col-item {
    font-weight: 500;
  }

  /* Comment column – softer */
  tbody td.col-comment {
    color: var(--text-muted);
    max-width: 280px;
  }

  /* ─── Empty State ─── */
  .empty-state {
    text-align: center;
    padding: 56px 24px;
    color: var(--text-muted);
  }
  .empty-state .empty-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 16px;
    opacity: 0.35;
  }
  .empty-state p {
    font-size: 15px;
  }

  /* ─── Animations ─── */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0);    }
  }

  /* ═══════════════════════════════════════════
     RESPONSIVE
     ═══════════════════════════════════════════ */

  /* Tablet */
  @media (max-width: 768px) {
    .content-wrapper {
      margin-left: 72px;
      padding: 32px 24px;
    }
    .summary-cards {
      grid-template-columns: 1fr;
      gap: 24px;
    }
    .page-header h1 { font-size: 32px; }
  }

  /* Mobile */
  @media (max-width: 480px) {
    .content-wrapper {
      margin-left: 0;
      padding: 24px 16px;
    }
    .page-header h1 { font-size: 28px; }
    .summary-cards .card { padding: 24px; }

    /* Horizontal-scroll the table on small screens */
    .table-wrapper { overflow-x: auto; }
    table { min-width: 640px; }
  }
</style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
<main class="main-content">

  <!-- ── Page Header ── -->
  <div class="page-header">
    <h1>Customer Feedback</h1>
    <p>Review customer ratings and comments, or open tablet mode for new feedback</p>
  </div>

  <!-- ── Summary Cards ── -->
  <?php
  $avg_ratings = $conn->query("
      SELECT i.name AS item_name,
             ROUND(AVG(f.rating), 1) AS avg_rating,
             COUNT(f.id) AS total_reviews
      FROM feedback f
      JOIN items i ON f.item_id = i.id
      GROUP BY f.item_id
      ORDER BY avg_rating DESC
  ")->fetch_all(MYSQLI_ASSOC);
  ?>

  <section class="summary-cards">

    <!-- Average Rating Card -->
    <div class="card">
      <h3>Average Rating</h3>
      <?php if (empty($avg_ratings)): ?>
        <p style="color:var(--text-muted);font-size:14px;">No ratings recorded yet.</p>
      <?php else: ?>
        <?php foreach ($avg_ratings as $r): ?>
          <div class="item-row">
            <span class="item-name"><?= htmlspecialchars($r['item_name']) ?></span>
            <span class="item-value">
              <span class="stars-gold">
                <?php
                  $full  = (int) floor($r['avg_rating']);
                  $empty = 5 - $full;
                  echo str_repeat('★', $full);
                  echo '<span class="star-empty">' . str_repeat('★', $empty) . '</span>';
                ?>
              </span>
              &nbsp;<strong style="font-size:13px;color:var(--accent-primary);"><?= $r['avg_rating'] ?></strong>
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Total Reviews Card -->
    <div class="card">
      <h3>Total Reviews</h3>
      <?php if (empty($avg_ratings)): ?>
        <p style="color:var(--text-muted);font-size:14px;">No reviews recorded yet.</p>
      <?php else: ?>
        <?php foreach ($avg_ratings as $r): ?>
          <div class="item-row">
            <span class="item-name"><?= htmlspecialchars($r['item_name']) ?></span>
            <span class="review-badge"><?= $r['total_reviews'] ?> review<?= $r['total_reviews'] > 1 ? 's' : '' ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </section>

  <!-- ── Open Tablet Feedback Button ── -->
  <div class="tablet-btn-row">
    <a href="feedback-tablet.php" target="_blank" class="btn-primary">
      <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12" y2="18.01"/></svg>
      Open Tablet Feedback
    </a>
  </div>

  <!-- ── Full Feedback Table ── -->
  <p class="section-label">All Feedback</p>
  <div class="table-wrapper">
    <?php
    $feedbacks = $conn->query("
        SELECT f.id, f.created_at, c.name AS customer_name,
               i.name AS item_name, f.rating, f.comment
        FROM feedback f
        JOIN customers c ON f.customer_id = c.id
        JOIN items i     ON f.item_id     = i.id
        ORDER BY f.created_at DESC
    ");
    ?>

    <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date &amp; Time</th>
          <th>Customer</th>
          <th>Item</th>
          <th>Rating</th>
          <th>Comment</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $feedbacks->fetch_assoc()): ?>
        <tr>
          <td class="col-id"><?= $row['id'] ?></td>
          <td class="col-date"><?= htmlspecialchars($row['created_at']) ?></td>
          <td class="col-name"><?= htmlspecialchars($row['customer_name']) ?></td>
          <td class="col-item"><?= htmlspecialchars($row['item_name']) ?></td>
          <td>
            <span class="stars-gold">
              <?php
                $full  = (int) $row['rating'];
                $empty = 5 - $full;
                echo str_repeat('★', $full);
                echo '<span class="star-empty">' . str_repeat('★', $empty) . '</span>';
              ?>
            </span>
          </td>
          <td class="col-comment"><?= htmlspecialchars($row['comment']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <?php else: ?>
      <div class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="var(--bg-secondary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <p>No feedback has been submitted yet.</p>
      </div>
    <?php endif; ?>
  </div>

</main>
</div>
</body>
</html>
<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

// Get filter values if submitted
$from_date = $_GET['from-date'] ?? '';
$to_date   = $_GET['to-date'] ?? '';
$search    = $_GET['search'] ?? '';

$where = [];
if($from_date) $where[] = "o.order_time >= '".date('Y-m-d 00:00:00', strtotime($from_date))."'";
if($to_date)   $where[] = "o.order_time <= '".date('Y-m-d 23:59:59', strtotime($to_date))."'";
if($search)    $where[] = "(o.id LIKE '%". $conn->real_escape_string($search) ."%' OR i.name LIKE '%". $conn->real_escape_string($search) ."%' OR c.name LIKE '%". $conn->real_escape_string($search) ."%')";

$where_sql = $where ? "WHERE ".implode(' AND ', $where) : "";

// Fetch orders with items and customer
$sql = "SELECT o.id as order_id, o.order_time, c.name as customer_name,
               i.name as item_name, oi.quantity, oi.total
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN items i ON oi.item_id = i.id
        $where_sql
        ORDER BY o.order_time DESC";

$result = $conn->query($sql);
$orders = [];
while($row = $result->fetch_assoc()){
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History - POS System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --bg-primary: #FBF9F5;
      --bg-secondary: #CEC3C1;
      --accent-primary: #630116;
      --accent-secondary: #AF5B73;
      --accent-success: #7E892B;
      --text-dark: #2a2a2a;
      --text-muted: #6a6a6a;
    }

    body {
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      color: var(--text-dark);
      min-height: 100vh;
    }

    /* Content Area */
    .content-wrapper {
      margin-left: 260px;
      min-height: 100vh;
      padding: 48px 40px;
      transition: margin-left 0.3s ease;
    }

    .main-content {
      max-width: 1400px;
      margin: 0 auto;
    }

    /* Page Header */
    .page-header {
      margin-bottom: 48px;
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .page-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 40px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 12px;
      letter-spacing: -0.5px;
    }

    .page-header p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* Filter Section */
    .filter-section {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 24px 32px;
      margin-bottom: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.1s backwards;
    }

    .filter-form {
      display: grid;
      grid-template-columns: 1fr 1fr 2fr auto;
      gap: 16px;
      align-items: end;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .filter-group label {
      font-size: 13px;
      font-weight: 500;
      color: var(--text-dark);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .filter-group input {
      padding: 12px 16px;
      font-size: 15px;
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      color: var(--text-dark);
      transition: all 0.2s ease;
    }

    .filter-group input::placeholder {
      color: #999;
    }

    .filter-group input:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175, 91, 115, 0.1);
    }

    .filter-btn {
      padding: 12px 28px;
      font-size: 14px;
      font-weight: 600;
      font-family: 'Work Sans', sans-serif;
      background: var(--accent-primary);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
      height: fit-content;
    }

    .filter-btn:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.25);
    }

    .filter-btn:active {
      transform: translateY(0);
    }

    /* Order Table Section */
    .order-table-section {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .order-table {
      width: 100%;
      border-collapse: collapse;
    }

    .order-table thead {
      background: var(--bg-primary);
      border-bottom: 2px solid var(--bg-secondary);
    }

    .order-table th {
      padding: 16px 20px;
      text-align: left;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-dark);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    .order-table tbody tr {
      border-bottom: 1px solid var(--bg-secondary);
      transition: all 0.2s ease;
    }

    .order-table tbody tr:last-child {
      border-bottom: none;
    }

    .order-table tbody tr:hover {
      background: #f5f3ef;
    }

    .order-table td {
      padding: 16px 20px;
      font-size: 14px;
      color: var(--text-dark);
      vertical-align: middle;
    }

    .order-table td:first-child {
      font-weight: 600;
      color: var(--accent-primary);
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 32px;
      color: var(--text-muted);
    }

    .empty-state svg {
      width: 64px;
      height: 64px;
      margin-bottom: 16px;
      stroke: var(--bg-secondary);
    }

    .empty-state p {
      font-size: 16px;
      margin-bottom: 8px;
    }

    .empty-state small {
      font-size: 14px;
      color: var(--text-muted);
    }

    /* Invoice Button */
    .invoice-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      background: var(--bg-primary);
      color: var(--accent-secondary);
      text-decoration: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 500;
      border: 1px solid var(--bg-secondary);
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .invoice-btn:hover {
      background: var(--accent-secondary);
      color: white;
      border-color: var(--accent-secondary);
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(175, 91, 115, 0.2);
    }

    .invoice-btn svg {
      width: 14px;
      height: 14px;
      stroke: currentColor;
    }

    /* Badge for Quantity */
    .quantity-badge {
      display: inline-block;
      padding: 4px 10px;
      background: var(--accent-success);
      color: white;
      font-size: 12px;
      font-weight: 600;
      border-radius: 4px;
    }

    /* Price Styling */
    .price {
      font-weight: 600;
      color: var(--accent-primary);
      font-size: 15px;
    }

    /* Date Styling */
    .date-time {
      font-size: 14px;
      color: var(--text-muted);
    }

    .date-time strong {
      display: block;
      color: var(--text-dark);
      font-weight: 500;
      margin-bottom: 2px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .filter-form {
        grid-template-columns: 1fr 1fr;
      }

      .filter-group:nth-child(3) {
        grid-column: 1 / -1;
      }

      .filter-btn {
        grid-column: 1 / -1;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 72px;
        padding: 32px 24px;
      }

      .page-header h1 {
        font-size: 32px;
      }

      .filter-form {
        grid-template-columns: 1fr;
      }

      .filter-section {
        padding: 20px 24px;
      }

      .order-table th,
      .order-table td {
        padding: 12px 16px;
        font-size: 13px;
      }

      .order-table th {
        font-size: 11px;
      }
    }

    @media (max-width: 480px) {
      .content-wrapper {
        margin-left: 0;
        padding: 24px 16px;
      }

      .page-header h1 {
        font-size: 28px;
      }

      .filter-section {
        padding: 16px 20px;
      }

      .order-table th,
      .order-table td {
        padding: 10px 12px;
        font-size: 12px;
      }

      /* Stack table on very small screens */
      .order-table thead {
        display: none;
      }

      .order-table tbody tr {
        display: block;
        margin-bottom: 16px;
        border: 1px solid var(--bg-secondary);
        border-radius: 6px;
        padding: 12px;
      }

      .order-table td {
        display: block;
        text-align: left;
        padding: 8px 0;
        border: none;
      }

      .order-table td:before {
        content: attr(data-label);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 4px;
      }

      .invoice-btn {
        width: 100%;
        justify-content: center;
        margin-top: 8px;
      }
    }
  </style>
</head>
<body>

  <?php include '../includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <!-- Page Header -->
      <header class="page-header">
        <h1>Order History</h1>
        <p>View past orders and download invoices</p>
      </header>

      <!-- Filter Section -->
      <section class="filter-section">
        <form method="GET" class="filter-form">
          <div class="filter-group">
            <label for="from-date">From Date</label>
            <input 
              type="date" 
              id="from-date" 
              name="from-date" 
              value="<?= htmlspecialchars($from_date) ?>"
            />
          </div>

          <div class="filter-group">
            <label for="to-date">To Date</label>
            <input 
              type="date" 
              id="to-date" 
              name="to-date" 
              value="<?= htmlspecialchars($to_date) ?>"
            />
          </div>

          <div class="filter-group">
            <label for="search">Search</label>
            <input 
              type="text" 
              id="search" 
              name="search" 
              placeholder="Order ID, Item Name, or Customer..." 
              value="<?= htmlspecialchars($search) ?>"
            />
          </div>

          <button type="submit" class="filter-btn">Apply Filters</button>
        </form>
      </section>

      <!-- Order Table -->
      <section class="order-table-section">
        <?php if($orders): ?>
          <div class="table-wrapper">
            <table class="order-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date & Time</th>
                  <th>Item Name</th>
                  <th>Quantity</th>
                  <th>Total Price</th>
                  <th>Customer Name</th>
                  <th>Invoice</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($orders as $order): ?>
                  <tr>
                    <td data-label="Order ID">#<?= $order['order_id'] ?></td>
                    <td data-label="Date & Time">
                      <div class="date-time">
                        <strong><?= date('d M Y', strtotime($order['order_time'])) ?></strong>
                        <?= date('H:i:s', strtotime($order['order_time'])) ?>
                      </div>
                    </td>
                    <td data-label="Item Name"><?= htmlspecialchars($order['item_name']) ?></td>
                    <td data-label="Quantity">
                      <span class="quantity-badge"><?= $order['quantity'] ?></span>
                    </td>
                    <td data-label="Total Price">
                      <span class="price">₹<?= number_format($order['total'], 2) ?></span>
                    </td>
                    <td data-label="Customer Name"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td data-label="Invoice">
                      <a href="../handlers/invoice.php?order_id=<?= $order['order_id'] ?>" class="invoice-btn" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        View Invoice
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
            <p>No orders found</p>
            <small>Try adjusting your filters or search terms</small>
          </div>
        <?php endif; ?>
      </section>

    </main>
  </div>

</body>
</html>
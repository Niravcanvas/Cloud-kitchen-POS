<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include DB connection (updated path for root location)
include __DIR__ . '/config/dbcon.php';

// Get username
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kitchen Dashboard - CakeCafe POS</title>
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* ===== CSS VARIABLES ===== */
    :root {
      --bg-primary: #FBF9F5;
      --bg-secondary: #CEC3C1;
      --accent-primary: #630116;
      --accent-secondary: #AF5B73;
      --accent-success: #7E892B;
      --text-dark: #2a2a2a;
      --text-muted: #6a6a6a;
    }

    /* ===== RESET & BASE ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      color: var(--text-dark);
      line-height: 1.6;
      min-height: 100vh;
    }

    /* ===== LAYOUT ===== */
    .main-content {
      margin-left: 260px;
      padding: 48px 40px;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    /* ===== PAGE HEADER ===== */
    .page-header {
      margin-bottom: 48px;
      animation: fadeIn 0.5s ease-out;
    }

    .page-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 40px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .page-header p {
      font-family: 'Work Sans', sans-serif;
      font-size: 16px;
      color: var(--text-muted);
      font-weight: 400;
    }

    /* ===== SUMMARY CARDS ===== */
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 48px;
      animation: fadeIn 0.6s ease-out 0.1s backwards;
    }

    .summary-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      transition: all 0.3s ease;
    }

    .summary-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(99, 1, 22, 0.12);
      border-color: var(--accent-secondary);
    }

    .summary-card h3 {
      font-family: 'Work Sans', sans-serif;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 16px;
    }

    .summary-card .value {
      font-family: 'Crimson Pro', serif;
      font-size: 36px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .summary-card .description {
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .summary-card .clock {
      font-family: 'Crimson Pro', serif;
      font-size: 32px;
      font-weight: 600;
      color: var(--accent-primary);
      font-variant-numeric: tabular-nums;
      letter-spacing: -0.3px;
    }

    .summary-card .date {
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      color: var(--accent-success);
      margin-top: 4px;
    }

    .summary-card .logout-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--accent-secondary);
      text-decoration: none;
      font-family: 'Work Sans', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 12px 24px;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      transition: all 0.2s ease;
      margin-top: 12px;
    }

    .summary-card .logout-link:hover {
      background: var(--accent-secondary);
      color: white;
      border-color: var(--accent-secondary);
      transform: translateY(-1px);
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      margin-bottom: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .filter-form {
      display: flex;
      gap: 16px;
      align-items: flex-end;
      flex-wrap: wrap;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex: 1;
      min-width: 200px;
    }

    .form-group label {
      font-family: 'Work Sans', sans-serif;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-dark);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-group input {
      height: 44px;
      padding: 14px 16px;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      color: var(--text-dark);
      transition: all 0.2s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175, 91, 115, 0.1);
    }

    .form-group input::placeholder {
      color: #999;
    }

    .filter-btn {
      height: 44px;
      padding: 0 24px;
      background: var(--accent-primary);
      color: white;
      border: none;
      border-radius: 6px;
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .filter-btn:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.25);
    }

    .filter-btn:active {
      transform: translateY(0);
    }

    /* ===== ORDER TABLE ===== */
    .order-section {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.3s backwards;
    }

    .section-header {
      padding: 32px;
      border-bottom: 1px solid var(--bg-secondary);
    }

    .section-header h2 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.3px;
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
    }

    .order-table th {
      padding: 16px 24px;
      text-align: left;
      font-family: 'Work Sans', sans-serif;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-dark);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid var(--bg-secondary);
    }

    .order-table tbody tr {
      border-bottom: 1px solid var(--bg-secondary);
      transition: background 0.15s ease;
    }

    .order-table tbody tr:hover {
      background: var(--bg-primary);
    }

    .order-table tbody tr:last-child {
      border-bottom: none;
    }

    .order-table td {
      padding: 16px 24px;
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      color: var(--text-dark);
    }

    .order-id {
      font-weight: 600;
      color: var(--accent-primary);
    }

    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      font-family: 'Work Sans', sans-serif;
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .status-completed {
      background: #f0f4e8;
      color: var(--accent-success);
      border: 1px solid var(--accent-success);
    }

    .status-pending {
      background: rgba(175, 91, 115, 0.1);
      color: var(--accent-secondary);
      border: 1px solid var(--accent-secondary);
    }

    .status-preparing {
      background: #fff5f5;
      color: var(--accent-primary);
      border: 1px solid var(--accent-primary);
    }

    .invoice-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      background: var(--bg-primary);
      color: var(--accent-secondary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      text-decoration: none;
      font-family: 'Work Sans', sans-serif;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .invoice-btn:hover {
      background: var(--accent-secondary);
      color: white;
      border-color: var(--accent-secondary);
      transform: translateY(-1px);
    }

    .invoice-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .empty-state {
      text-align: center;
      padding: 64px 24px;
      color: var(--text-muted);
    }

    .empty-state svg {
      width: 64px;
      height: 64px;
      margin-bottom: 16px;
      stroke: var(--bg-secondary);
    }

    .empty-state h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 8px;
      letter-spacing: -0.3px;
    }

    .empty-state p {
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      color: var(--text-muted);
    }

    /* ===== ANIMATIONS ===== */
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

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .main-content {
        margin-left: 72px;
        padding: 32px 24px;
      }

      .page-header h1 {
        font-size: 30px;
      }

      .summary-cards {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .filter-form {
        flex-direction: column;
        align-items: stretch;
      }

      .form-group {
        min-width: 100%;
      }

      .filter-btn {
        width: 100%;
      }
    }

    @media (max-width: 480px) {
      .main-content {
        margin-left: 0;
        padding: 24px 16px;
      }

      .page-header h1 {
        font-size: 28px;
      }

      .summary-card {
        padding: 24px;
      }

      .order-table th,
      .order-table td {
        padding: 12px 16px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

  <?php include 'includes/sidebar.php'; ?>

  <div class="main-content">

    <!-- Page Header -->
    <div class="page-header">
      <h1>Kitchen Dashboard</h1>
      <p>Track sales, orders, and optimize your menu—everything your cake café needs</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="summary-card">
        <h3>Current Date & Time</h3>
        <div class="clock" id="live-clock"></div>
        <div class="date" id="live-date"></div>
      </div>

      <div class="summary-card">
        <h3>Welcome Back</h3>
        <div class="value"><?php echo htmlspecialchars($username); ?></div>
        <p class="description">Ready to manage your kitchen operations</p>
        <a href="handlers/logout.php" class="logout-link">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>

      <div class="summary-card">
        <h3>Revenue Update</h3>
        <div class="value">+8%</div>
        <p class="description">Sales up compared to yesterday. Focus on top performing items for continued growth.</p>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <form class="filter-form">
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" />
        </div>
        <div class="form-group">
          <label for="search">Search</label>
          <input type="text" id="search" name="search" placeholder="Order ID, customer name..." />
        </div>
        <button type="submit" class="filter-btn">
          <i class="fas fa-filter"></i> Apply Filter
        </button>
      </form>
    </div>

    <!-- Order Table -->
    <div class="order-section">
      <div class="section-header">
        <h2>Recent Orders</h2>
      </div>
      <div class="table-wrapper">
        <table class="order-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Invoice</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Fetch orders with customer name and invoice file path
            $sql = "SELECT o.id AS order_id, c.name AS customer_name, o.order_time, o.status, i.file_path
                    FROM orders o
                    LEFT JOIN customers c ON o.customer_id = c.id
                    LEFT JOIN invoices i ON o.id = i.order_id
                    ORDER BY o.order_time DESC
                    LIMIT 50";
            
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $orderId = $row['order_id'];
                    $customerName = htmlspecialchars($row['customer_name'] ?? 'Guest');
                    $orderDate = date("d M Y", strtotime($row['order_time']));
                    $status = strtolower($row['status']);
                    $invoiceLink = $row['file_path'] ? htmlspecialchars($row['file_path']) : null;
                    
                    // Determine status class
                    $statusClass = 'status-pending';
                    if ($status === 'completed') {
                        $statusClass = 'status-completed';
                    } elseif ($status === 'preparing') {
                        $statusClass = 'status-preparing';
                    }

                    echo "<tr>
                            <td class='order-id'>#" . str_pad($orderId, 4, '0', STR_PAD_LEFT) . "</td>
                            <td>{$customerName}</td>
                            <td>{$orderDate}</td>
                            <td><span class='status-badge {$statusClass}'>{$status}</span></td>
                            <td>";
                    
                    if ($invoiceLink) {
                        echo "<a href='{$invoiceLink}' class='invoice-btn' target='_blank'>
                                <i class='fas fa-file-invoice'></i> View
                              </a>";
                    } else {
                        echo "<span class='invoice-btn disabled'>
                                <i class='fas fa-file-invoice'></i> N/A
                              </span>";
                    }
                    
                    echo "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>
                        <div class='empty-state'>
                          <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                            <circle cx='12' cy='12' r='10'/>
                            <path d='M16 16s-1.5-2-4-2-4 2-4 2'/>
                            <line x1='9' y1='9' x2='9.01' y2='9'/>
                            <line x1='15' y1='9' x2='15.01' y2='9'/>
                          </svg>
                          <h3>No Orders Found</h3>
                          <p>There are currently no orders to display</p>
                        </div>
                      </td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script>
    // Live Clock Update
    function updateClock() {
      const now = new Date();
      
      // Time
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      
      // Date
      const day = String(now.getDate()).padStart(2, '0');
      const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      const month = monthNames[now.getMonth()];
      const year = now.getFullYear();
      const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      const dayName = dayNames[now.getDay()];

      document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
      document.getElementById('live-date').textContent = `${dayName}, ${day} ${month} ${year}`;
    }

    // Initialize and update every second
    updateClock();
    setInterval(updateClock, 1000);
  </script>

</body>
</html>
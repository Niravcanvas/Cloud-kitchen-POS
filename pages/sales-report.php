<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
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

// Calculate Summary Metrics
$totalRevenue = 0;
$totalOrders = 0;
$itemSales = [];

foreach($orders as $order){
    $totalRevenue += $order['total'];
    if(!isset($itemSales[$order['item_name']])){
        $itemSales[$order['item_name']] = 0;
    }
    $itemSales[$order['item_name']] += $order['quantity'];
}

$uniqueOrders = array_unique(array_column($orders, 'order_id'));
$totalOrders = count($uniqueOrders);
$avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

// Find top-selling item
arsort($itemSales);
$topSellingItem = count($itemSales) > 0 ? array_key_first($itemSales) : 'N/A';
$topSellingQty = count($itemSales) > 0 ? reset($itemSales) : 0;

// Orders by Status
$statusData = [];
$statusQuery = $conn->query("SELECT status, COUNT(*) as count FROM orders $where_sql GROUP BY status");
while($row = $statusQuery->fetch_assoc()){
    $statusData[$row['status']] = $row['count'];
}

// Revenue by Item
$itemRevenueData = [];
$itemQuery = $conn->query("SELECT i.name, SUM(oi.total) as revenue 
                           FROM order_items oi 
                           JOIN items i ON oi.item_id = i.id 
                           JOIN orders o ON o.id = oi.order_id 
                           $where_sql
                           GROUP BY i.name
                           ORDER BY revenue DESC");
while($row = $itemQuery->fetch_assoc()){
    $itemRevenueData[$row['name']] = $row['revenue'];
}

// Daily Revenue Trend
$dailyRevenueData = [];
$dailyQuery = $conn->query("SELECT DATE(o.order_time) as order_date, SUM(oi.total) as revenue
                            FROM orders o
                            JOIN order_items oi ON o.id = oi.order_id
                            $where_sql
                            GROUP BY DATE(o.order_time)
                            ORDER BY order_date ASC");
while($row = $dailyQuery->fetch_assoc()){
    $dailyRevenueData[$row['order_date']] = $row['revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Report - POS System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    /* Summary Cards Grid */
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 24px;
      margin-bottom: 48px;
    }

    .summary-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 28px 24px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      transition: all 0.3s ease;
      animation: fadeIn 0.6s ease-out backwards;
    }

    .summary-card:nth-child(1) { animation-delay: 0.2s; }
    .summary-card:nth-child(2) { animation-delay: 0.25s; }
    .summary-card:nth-child(3) { animation-delay: 0.3s; }
    .summary-card:nth-child(4) { animation-delay: 0.35s; }

    .summary-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(99, 1, 22, 0.12);
      border-color: var(--accent-secondary);
    }

    .summary-card h3 {
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      margin-bottom: 12px;
    }

    .summary-card .value {
      font-family: 'Crimson Pro', serif;
      font-size: 32px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.5px;
      line-height: 1.2;
    }

    .summary-card .sub-value {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 400;
    }

    /* Highlight cards */
    .summary-card.revenue {
      border-left: 4px solid var(--accent-success);
    }

    .summary-card.orders {
      border-left: 4px solid var(--accent-primary);
    }

    .summary-card.average {
      border-left: 4px solid var(--accent-secondary);
    }

    .summary-card.top-item {
      border-left: 4px solid var(--accent-success);
    }

    /* Charts Section */
    .charts-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      margin-bottom: 48px;
    }

    .chart-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out backwards;
    }

    .chart-card:nth-child(1) { animation-delay: 0.4s; }
    .chart-card:nth-child(2) { animation-delay: 0.45s; }
    .chart-card:nth-child(3) { animation-delay: 0.5s; }

    .chart-card.full-width {
      grid-column: 1 / -1;
    }

    .chart-card h4 {
      font-family: 'Crimson Pro', serif;
      font-size: 20px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 24px;
      letter-spacing: -0.3px;
    }

    .chart-box {
      width: 100%;
      height: 320px;
      position: relative;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .charts-section {
        grid-template-columns: 1fr;
      }

      .filter-form {
        grid-template-columns: 1fr 1fr;
      }

      .filter-group:nth-child(3) {
        grid-column: 1 / -1;
      }

      .filter-btn {
        grid-column: 1 / -1;
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

      .summary-cards {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .filter-form {
        grid-template-columns: 1fr;
      }

      .filter-section {
        padding: 20px 24px;
      }

      .chart-card {
        padding: 24px 20px;
      }

      .summary-card .value {
        font-size: 28px;
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

      .summary-card {
        padding: 20px;
      }

      .chart-box {
        height: 280px;
      }

      .summary-card .value {
        font-size: 24px;
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
        <h1>Sales Report</h1>
        <p>View detailed revenue and sales trends for your cloud kitchen</p>
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
              placeholder="Item or Customer Name..."
              value="<?= htmlspecialchars($search) ?>"
            />
          </div>

          <button type="submit" class="filter-btn">Apply Filters</button>
        </form>
      </section>

      <!-- Summary Cards -->
      <section class="summary-cards">
        
        <div class="summary-card revenue">
          <h3>Total Revenue</h3>
          <div class="value">₹<?= number_format($totalRevenue, 2) ?></div>
          <div class="sub-value">From all orders</div>
        </div>

        <div class="summary-card orders">
          <h3>Total Orders</h3>
          <div class="value"><?= $totalOrders ?></div>
          <div class="sub-value">Unique orders placed</div>
        </div>

        <div class="summary-card average">
          <h3>Average Order Value</h3>
          <div class="value">₹<?= number_format($avgOrderValue, 2) ?></div>
          <div class="sub-value">Per order average</div>
        </div>

        <div class="summary-card top-item">
          <h3>Top-Selling Item</h3>
          <div class="value"><?= htmlspecialchars($topSellingItem) ?></div>
          <div class="sub-value"><?= $topSellingQty ?> units sold</div>
        </div>

      </section>

      <!-- Charts Section -->
      <section class="charts-section">
        
        <div class="chart-card full-width">
          <h4>Revenue by Item</h4>
          <div class="chart-box">
            <canvas id="revenueItemChart"></canvas>
          </div>
        </div>

        <div class="chart-card full-width">
          <h4>Daily Revenue Trend</h4>
          <div class="chart-box">
            <canvas id="dailyTrendChart"></canvas>
          </div>
        </div>

        <div class="chart-card">
          <h4>Orders by Status</h4>
          <div class="chart-box">
            <canvas id="statusChart"></canvas>
          </div>
        </div>

      </section>

    </main>
  </div>

  <script>
    // Chart.js global configuration
    Chart.defaults.font.family = "'Work Sans', sans-serif";
    Chart.defaults.color = '#2a2a2a';

    // Revenue by Item (Bar Chart)
    const itemLabels = <?php echo json_encode(array_keys($itemRevenueData)); ?>;
    const itemRevenues = <?php echo json_encode(array_values($itemRevenueData)); ?>;

    new Chart(document.getElementById('revenueItemChart'), {
      type: 'bar',
      data: {
        labels: itemLabels,
        datasets: [{
          label: 'Revenue (₹)',
          data: itemRevenues,
          backgroundColor: '#AF5B73',
          borderColor: '#630116',
          borderWidth: 1,
          borderRadius: 4,
          hoverBackgroundColor: '#630116'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              font: { size: 13, weight: '500' },
              padding: 16
            }
          },
          tooltip: {
            backgroundColor: '#630116',
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            padding: 12,
            borderColor: '#CEC3C1',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return 'Revenue: ₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₹' + value.toLocaleString('en-IN');
              },
              font: { size: 12 }
            },
            grid: {
              color: '#f5f3ef'
            }
          },
          x: {
            ticks: {
              font: { size: 12 }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Daily Revenue Trend (Line Chart)
    const dailyLabels = <?php echo json_encode(array_keys($dailyRevenueData)); ?>;
    const dailyRevenues = <?php echo json_encode(array_values($dailyRevenueData)); ?>;

    new Chart(document.getElementById('dailyTrendChart'), {
      type: 'line',
      data: {
        labels: dailyLabels,
        datasets: [{
          label: 'Daily Revenue (₹)',
          data: dailyRevenues,
          borderColor: '#630116',
          backgroundColor: 'rgba(99, 1, 22, 0.1)',
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 5,
          pointBackgroundColor: '#630116',
          pointBorderColor: '#FBF9F5',
          pointBorderWidth: 2,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            labels: {
              font: { size: 13, weight: '500' },
              padding: 16
            }
          },
          tooltip: {
            backgroundColor: '#630116',
            padding: 12,
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            callbacks: {
              label: function(context) {
                return 'Revenue: ₹' + context.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2});
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₹' + value.toLocaleString('en-IN');
              },
              font: { size: 12 }
            },
            grid: {
              color: '#f5f3ef'
            }
          },
          x: {
            ticks: {
              font: { size: 12 }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Orders by Status (Pie Chart)
    const orderStatusLabels = <?php echo json_encode(array_keys($statusData)); ?>;
    const orderStatusCounts = <?php echo json_encode(array_values($statusData)); ?>;

    new Chart(document.getElementById('statusChart'), {
      type: 'pie',
      data: {
        labels: orderStatusLabels,
        datasets: [{
          data: orderStatusCounts,
          backgroundColor: ['#630116', '#AF5B73', '#7E892B', '#CEC3C1'],
          borderColor: '#FBF9F5',
          borderWidth: 3,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: { size: 12 },
              padding: 16,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: '#630116',
            padding: 12,
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            callbacks: {
              label: function(context) {
                return context.label + ': ' + context.parsed + ' orders';
              }
            }
          }
        }
      }
    });
  </script>

</body>
</html>
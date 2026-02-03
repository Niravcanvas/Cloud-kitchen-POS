<?php
// forecast.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include __DIR__ . '/../config/dbcon.php';

// Initialize arrays for charts
$forecast_orders = [];
$actual_orders = [];
$item_labels = [];
$item_orders = [];
$hour_labels = [];
$hour_orders = [];
$mini_labels = [];
$mini_orders = [];

$days_of_week = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
// Initialize counts with 0 for each day
foreach ($days_of_week as $day) {
  $forecast_orders[$day] = 0;
  $actual_orders[$day] = 0;
}

// 1. Get actual completed orders count per weekday (last 7 days)
$sql_actual = "SELECT DAYNAME(order_time) AS dayname, COUNT(*) AS order_count 
               FROM orders 
               WHERE status = 'Completed' 
               AND order_time >= CURDATE() - INTERVAL 7 DAY
               GROUP BY dayname";

$result_actual = $conn->query($sql_actual);
if ($result_actual) {
  while ($row = $result_actual->fetch_assoc()) {
    $day = $row['dayname'];
    if (array_key_exists($day, $actual_orders)) {
      $actual_orders[$day] = (int)$row['order_count'];
    }
  }
}

// 2. Get total orders count per weekday (forecast approximation) last 7 days
$sql_forecast = "SELECT DAYNAME(order_time) AS dayname, COUNT(*) AS order_count 
                 FROM orders 
                 WHERE order_time >= CURDATE() - INTERVAL 7 DAY
                 GROUP BY dayname";

$result_forecast = $conn->query($sql_forecast);
if ($result_forecast) {
  while ($row = $result_forecast->fetch_assoc()) {
    $day = $row['dayname'];
    if (array_key_exists($day, $forecast_orders)) {
      $forecast_orders[$day] = (int)$row['order_count'];
    }
  }
}

// Reorder arrays in Mon-Sun order
$forecast_orders_arr = [];
$actual_orders_arr = [];
foreach ($days_of_week as $day) {
  $forecast_orders_arr[] = $forecast_orders[$day];
  $actual_orders_arr[] = $actual_orders[$day];
}

// 3. Demand by item for completed orders last 7 days
$sql_items = "SELECT i.name, SUM(oi.quantity) AS total_quantity
              FROM order_items oi
              JOIN orders o ON oi.order_id = o.id
              JOIN items i ON oi.item_id = i.id
              WHERE o.status = 'Completed'
              AND o.order_time >= CURDATE() - INTERVAL 7 DAY
              GROUP BY i.id
              ORDER BY total_quantity DESC
              LIMIT 10";

$result_items = $conn->query($sql_items);
if ($result_items) {
  while ($row = $result_items->fetch_assoc()) {
    $item_labels[] = $row['name'];
    $item_orders[] = (int)$row['total_quantity'];
  }
}

// 4. Hourly demand heatmap (last 24 hours)
$sql_hourly = "SELECT HOUR(order_time) AS hour, COUNT(*) AS order_count
               FROM orders
               WHERE status = 'Completed'
               AND order_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
               GROUP BY hour
               ORDER BY hour";

$result_hourly = $conn->query($sql_hourly);
if ($result_hourly) {
  while ($row = $result_hourly->fetch_assoc()) {
    $hour_num = (int)$row['hour'];
    $ampm = ($hour_num >= 12) ? 'PM' : 'AM';
    $display_hour = sprintf("%02d:00 %s", ($hour_num % 12 ?: 12), $ampm);
    $hour_labels[] = $display_hour;
    $hour_orders[] = (int)$row['order_count'];
  }
}

// 5. Mini Peak Hour chart: top 3 peak hours
if (!empty($hour_labels) && !empty($hour_orders)) {
  $hour_data = array_combine($hour_labels, $hour_orders);
  arsort($hour_data);
  $top_hours = array_slice($hour_data, 0, 3, true);
  $mini_labels = array_keys($top_hours);
  $mini_orders = array_values($top_hours);
}

// 6. Summary statistics with safety checks
$total_forecast = array_sum($forecast_orders_arr);

if (!empty($item_orders)) {
  $max_order = max($item_orders);
  $top_item_idx = array_keys($item_orders, $max_order);
  $top_item = $item_labels[$top_item_idx[0]];
  $top_item_count = $max_order;
} else {
  $top_item = 'N/A';
  $top_item_count = 0;
}

$peak_hour = !empty($mini_labels) ? $mini_labels[0] : 'N/A';
$peak_hour_count = !empty($mini_orders) ? $mini_orders[0] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Demand Forecast - Cloud Kitchen</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600&display=swap');

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
      margin-bottom: 4px;
      letter-spacing: -0.5px;
    }

    .summary-card .label {
      font-family: 'Work Sans', sans-serif;
      font-size: 14px;
      color: var(--text-muted);
    }

    .summary-card .mini-chart {
      height: 80px;
      margin-top: 16px;
      position: relative;
    }

    /* ===== CHARTS CONTAINER ===== */
    .charts-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 32px;
      margin-bottom: 32px;
    }

    .chart-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      transition: all 0.3s ease;
      animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .chart-card:hover {
      box-shadow: 0 8px 24px rgba(99, 1, 22, 0.12);
      border-color: var(--accent-secondary);
    }

    .chart-card h4 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 24px;
      letter-spacing: -0.3px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .chart-card h4 i {
      font-size: 20px;
    }

    .chart-wrapper {
      position: relative;
      height: 300px;
      width: 100%;
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
    @media (max-width: 1200px) {
      .charts-container {
        grid-template-columns: 1fr;
      }
    }

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

      .chart-wrapper {
        height: 250px;
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

      .summary-card,
      .chart-card {
        padding: 24px;
      }
    }
  </style>
</head>
<body>

  <?php include '../includes/sidebar.php'; ?>

  <div class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
      <h1>Demand Forecast</h1>
      <p>Visual insights and trends based on predicted demand and historical data</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="summary-card">
        <h3>Total Forecasted Orders</h3>
        <div class="value"><?php echo number_format($total_forecast); ?></div>
        <p class="label">Last 7 days projection</p>
      </div>

      <div class="summary-card">
        <h3>Peak Hour</h3>
        <div class="value"><?php echo htmlspecialchars($peak_hour); ?></div>
        <p class="label"><?php echo number_format($peak_hour_count); ?> orders during peak time</p>
        <?php if (!empty($mini_labels)): ?>
        <div class="mini-chart">
          <canvas id="hourlyMini"></canvas>
        </div>
        <?php endif; ?>
      </div>

      <div class="summary-card">
        <h3>Top Selling Item</h3>
        <div class="value"><?php echo htmlspecialchars($top_item); ?></div>
        <p class="label"><?php echo number_format($top_item_count); ?> orders in last 7 days</p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-container">
      
      <!-- Forecasted vs Actual Orders -->
      <div class="chart-card">
        <h4><i class="fas fa-chart-line"></i> Forecasted vs Actual Orders</h4>
        <div class="chart-wrapper">
          <canvas id="ordersChart"></canvas>
        </div>
      </div>

      <!-- Demand by Item -->
      <div class="chart-card">
        <h4><i class="fas fa-chart-bar"></i> Demand by Item</h4>
        <div class="chart-wrapper">
          <canvas id="itemChart"></canvas>
        </div>
      </div>

      <!-- Hourly Demand Heatmap -->
      <div class="chart-card">
        <h4><i class="fas fa-clock"></i> Hourly Demand (Last 24 Hours)</h4>
        <div class="chart-wrapper">
          <canvas id="heatmapChart"></canvas>
        </div>
      </div>
      
    </div>

  </div>

<script>
  // Chart.js default configuration
  Chart.defaults.font.family = "'Work Sans', sans-serif";
  Chart.defaults.color = '#2a2a2a';

  // Data from PHP
  const daysOfWeek = <?php echo json_encode($days_of_week); ?>;
  const forecastOrders = <?php echo json_encode(array_values($forecast_orders_arr)); ?>;
  const actualOrders = <?php echo json_encode(array_values($actual_orders_arr)); ?>;
  const itemLabels = <?php echo json_encode($item_labels); ?>;
  const itemOrders = <?php echo json_encode($item_orders); ?>;
  const hourLabels = <?php echo json_encode($hour_labels); ?>;
  const hourOrders = <?php echo json_encode($hour_orders); ?>;
  const miniLabels = <?php echo json_encode($mini_labels); ?>;
  const miniOrders = <?php echo json_encode($mini_orders); ?>;

  // 1. Forecasted vs Actual Orders Chart
  new Chart(document.getElementById('ordersChart'), {
    type: 'line',
    data: {
      labels: daysOfWeek,
      datasets: [
        {
          label: 'Forecasted Orders',
          data: forecastOrders,
          borderColor: '#AF5B73',
          backgroundColor: 'rgba(175, 91, 115, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#AF5B73'
        },
        {
          label: 'Actual Orders',
          data: actualOrders,
          borderColor: '#630116',
          backgroundColor: 'rgba(99, 1, 22, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#630116'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            padding: 16,
            usePointStyle: true,
            font: { size: 13, weight: '500', family: "'Work Sans', sans-serif" },
            color: '#2a2a2a'
          }
        },
        tooltip: {
          backgroundColor: 'rgba(42, 42, 42, 0.95)',
          padding: 12,
          titleFont: { size: 14, weight: '600', family: "'Work Sans', sans-serif" },
          bodyFont: { size: 13, family: "'Work Sans', sans-serif" },
          cornerRadius: 6
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: '#CEC3C1', borderDash: [5, 5] },
          ticks: { font: { size: 12, family: "'Work Sans', sans-serif" }, color: '#6a6a6a' }
        },
        x: {
          grid: { display: false },
          ticks: { font: { size: 12, family: "'Work Sans', sans-serif" }, color: '#6a6a6a' }
        }
      }
    }
  });

  // 2. Demand by Item Chart
  new Chart(document.getElementById('itemChart'), {
    type: 'bar',
    data: {
      labels: itemLabels,
      datasets: [{
        label: 'Orders',
        data: itemOrders,
        backgroundColor: '#7E892B',
        borderColor: '#7E892B',
        borderWidth: 0,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(42, 42, 42, 0.95)',
          padding: 12,
          titleFont: { size: 14, weight: '600', family: "'Work Sans', sans-serif" },
          bodyFont: { size: 13, family: "'Work Sans', sans-serif" },
          cornerRadius: 6
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: '#CEC3C1', borderDash: [5, 5] },
          ticks: { font: { size: 12, family: "'Work Sans', sans-serif" }, color: '#6a6a6a' }
        },
        x: {
          grid: { display: false },
          ticks: { font: { size: 12, family: "'Work Sans', sans-serif" }, color: '#6a6a6a' }
        }
      }
    }
  });

  // 3. Hourly Demand Heatmap
  new Chart(document.getElementById('heatmapChart'), {
    type: 'bar',
    data: {
      labels: hourLabels,
      datasets: [{
        label: 'Orders',
        data: hourOrders,
        backgroundColor: '#AF5B73',
        borderColor: '#AF5B73',
        borderWidth: 0,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(42, 42, 42, 0.95)',
          padding: 12,
          titleFont: { size: 14, weight: '600', family: "'Work Sans', sans-serif" },
          bodyFont: { size: 13, family: "'Work Sans', sans-serif" },
          cornerRadius: 6
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: '#CEC3C1', borderDash: [5, 5] },
          ticks: { font: { size: 12, family: "'Work Sans', sans-serif" }, color: '#6a6a6a' }
        },
        x: {
          grid: { display: false },
          ticks: { 
            font: { size: 11, family: "'Work Sans', sans-serif" },
            color: '#6a6a6a',
            maxRotation: 45,
            minRotation: 45
          }
        }
      }
    }
  });

  // 4. Mini Peak Hour Chart
  <?php if (!empty($mini_labels)): ?>
  new Chart(document.getElementById('hourlyMini'), {
    type: 'bar',
    data: {
      labels: miniLabels,
      datasets: [{
        data: miniOrders,
        backgroundColor: '#7E892B',
        borderRadius: 4
      }]
    },
    options: {
      plugins: { 
        legend: { display: false },
        tooltip: { enabled: false }
      },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { display: false },
        y: { display: false }
      }
    }
  });
  <?php endif; ?>
</script>

</body>
</html>
<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$from_date = $_GET['from-date'] ?? '';
$to_date   = $_GET['to-date'] ?? '';
$search    = $_GET['search'] ?? '';

$where = [];
if($from_date) $where[] = "o.order_time >= '".date('Y-m-d 00:00:00', strtotime($from_date))."'";
if($to_date)   $where[] = "o.order_time <= '".date('Y-m-d 23:59:59', strtotime($to_date))."'";
if($search)    $where[] = "(o.id LIKE '%". $conn->real_escape_string($search) ."%' 
                       OR i.name LIKE '%". $conn->real_escape_string($search) ."%' 
                       OR c.name LIKE '%". $conn->real_escape_string($search) ."%')";
$where_sql = $where ? "WHERE ".implode(' AND ', $where) : "";

/* =====================
   1. Get revenue & qty per item
===================== */
$sql = "SELECT i.id, i.name, i.cost, oi.quantity, oi.total
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN items i ON oi.item_id = i.id
        $where_sql";
$res = $conn->query($sql);

$itemData = [];
while($r = $res->fetch_assoc()){
    $id = $r['id'];
    if(!$id) continue;
    if(!isset($itemData[$id])){
        $itemData[$id] = [
            'name' => $r['name'],
            'revenue' => 0,
            'qty' => 0,
            'cost' => $r['cost'] ?? 0
        ];
    }
    $itemData[$id]['revenue'] += $r['total'];
    $itemData[$id]['qty']     += $r['quantity'];
}

$highestMargin = ['name' => '-', 'margin' => 0];
$lowestPerform = ['name' => '-', 'value' => PHP_INT_MAX];
$bestSelling   = ['name' => '-', 'qty' => 0];
$worstSelling  = ['name' => '-', 'qty' => PHP_INT_MAX];
$totalMargin   = 0; $countMargin = 0;

foreach($itemData as $id => $d){
    $margin = 0;
    if($d['revenue'] > 0 && $d['cost'] > 0){
        // simple margin % calculation
        $margin = (($d['revenue'] - ($d['qty'] * $d['cost'])) / $d['revenue']) * 100;
        $totalMargin += $margin;
        $countMargin++;
    }

    if($margin > $highestMargin['margin']){
        $highestMargin = ['name'=>$d['name'], 'margin'=>round($margin,2)];
    }
    if($d['revenue'] < $lowestPerform['value']){
        $lowestPerform = ['name'=>$d['name'], 'value'=>$d['revenue']];
    }
    if($d['qty'] > $bestSelling['qty']){
        $bestSelling = ['name'=>$d['name'], 'qty'=>$d['qty']];
    }
    if($d['qty'] < $worstSelling['qty']){
        $worstSelling = ['name'=>$d['name'], 'qty'=>$d['qty']];
    }
}

$avgMargin = $countMargin ? round($totalMargin/$countMargin,2) : 0;

// TODO: Wastage - placeholder (need wastage column/table to compute real)
$totalWastage = "N/A";

// prepare arrays for charts
$itemNames = array_column($itemData, 'name');
$itemRevenues = array_column($itemData, 'revenue');
$itemQtys = array_column($itemData, 'qty');

$itemNamesJS   = json_encode($itemNames);
$itemRevenueJS = json_encode($itemRevenues);
$itemQtyJS     = json_encode($itemQtys);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Optimization - POS System</title>
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

    .filter-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr auto;
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

    .filter-group input:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175, 91, 115, 0.1);
    }

    .filter-btn {
      padding: 12px 24px;
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
    }

    .filter-btn:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.25);
    }

    /* Summary Cards Grid */
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    .summary-card:nth-child(5) { animation-delay: 0.4s; }
    .summary-card:nth-child(6) { animation-delay: 0.45s; }

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
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.3px;
      line-height: 1.3;
    }

    .summary-card .sub-value {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 400;
    }

    /* Highlight specific cards */
    .summary-card.success {
      border-left: 4px solid var(--accent-success);
    }

    .summary-card.warning {
      border-left: 4px solid var(--accent-secondary);
    }

    .summary-card.info {
      border-left: 4px solid var(--accent-primary);
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

    .chart-card:nth-child(1) { animation-delay: 0.5s; }
    .chart-card:nth-child(2) { animation-delay: 0.55s; }
    .chart-card:nth-child(3) { animation-delay: 0.6s; }
    .chart-card:nth-child(4) { animation-delay: 0.65s; }

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

      .filter-grid {
        grid-template-columns: 1fr 1fr;
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

      .filter-grid {
        grid-template-columns: 1fr;
      }

      .filter-section {
        padding: 20px 24px;
      }

      .chart-card {
        padding: 24px 20px;
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
    }
  </style>
</head>
<body>

  <?php include '../includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <!-- Page Header -->
      <header class="page-header">
        <h1>Menu Optimization</h1>
        <p>Analyze performance and optimize your menu based on key metrics</p>
      </header>

      <!-- Filter Section -->
      <section class="filter-section">
        <form method="GET" action="">
          <div class="filter-grid">
            <div class="filter-group">
              <label for="from-date">From Date</label>
              <input 
                type="date" 
                id="from-date"
                name="from-date" 
                value="<?= htmlspecialchars($from_date) ?>"
              >
            </div>
            <div class="filter-group">
              <label for="to-date">To Date</label>
              <input 
                type="date" 
                id="to-date"
                name="to-date" 
                value="<?= htmlspecialchars($to_date) ?>"
              >
            </div>
            <div class="filter-group">
              <label for="search">Search</label>
              <input 
                type="text" 
                id="search"
                name="search" 
                placeholder="Order ID, Item, Customer..."
                value="<?= htmlspecialchars($search) ?>"
              >
            </div>
            <button type="submit" class="filter-btn">Apply Filters</button>
          </div>
        </form>
      </section>

      <!-- Summary Cards -->
      <section class="summary-cards">
        
        <div class="summary-card success">
          <h3>Highest Margin Item</h3>
          <div class="value"><?= htmlspecialchars($highestMargin['name']) ?></div>
          <div class="sub-value"><?= $highestMargin['margin'] ?>% margin</div>
        </div>

        <div class="summary-card warning">
          <h3>Lowest Performing Item</h3>
          <div class="value"><?= htmlspecialchars($lowestPerform['name']) ?></div>
          <div class="sub-value">₹<?= number_format($lowestPerform['value'] == PHP_INT_MAX ? 0 : $lowestPerform['value'], 2) ?> revenue</div>
        </div>

        <div class="summary-card info">
          <h3>Total Wastage</h3>
          <div class="value"><?= $totalWastage ?></div>
          <div class="sub-value">Percentage of total</div>
        </div>

        <div class="summary-card success">
          <h3>Avg Contribution Margin</h3>
          <div class="value"><?= $avgMargin ?>%</div>
          <div class="sub-value">Across all items</div>
        </div>

        <div class="summary-card success">
          <h3>Best-Selling Item</h3>
          <div class="value"><?= htmlspecialchars($bestSelling['name']) ?></div>
          <div class="sub-value"><?= $bestSelling['qty'] ?> units sold</div>
        </div>

        <div class="summary-card warning">
          <h3>Worst-Selling Item</h3>
          <div class="value"><?= htmlspecialchars($worstSelling['name']) ?></div>
          <div class="sub-value"><?= $worstSelling['qty'] == PHP_INT_MAX ? 0 : $worstSelling['qty'] ?> units sold</div>
        </div>

      </section>

      <!-- Charts Section -->
      <section class="charts-section">
        
        <div class="chart-card full-width">
          <h4>Total Revenue by Item</h4>
          <div class="chart-box">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <div class="chart-card full-width">
          <h4>Menu Engineering Matrix</h4>
          <div class="chart-box">
            <canvas id="matrixChart"></canvas>
          </div>
        </div>

        <div class="chart-card">
          <h4>Items to Promote</h4>
          <div class="chart-box">
            <canvas id="promoteChart"></canvas>
          </div>
        </div>

        <div class="chart-card">
          <h4>Items to Drop / Rework</h4>
          <div class="chart-box">
            <canvas id="dropChart"></canvas>
          </div>
        </div>

      </section>

    </main>
  </div>

  <script>
    const itemNames = <?= $itemNamesJS ?>;
    const itemRevenue = <?= $itemRevenueJS ?>;
    const itemQty = <?= $itemQtyJS ?>;

    // Chart.js global configuration
    Chart.defaults.font.family = "'Work Sans', sans-serif";
    Chart.defaults.color = '#2a2a2a';

    // Revenue by Item (Bar Chart)
    new Chart(document.getElementById('revenueChart'), {
      type: 'bar',
      data: {
        labels: itemNames,
        datasets: [{
          label: 'Revenue (₹)',
          data: itemRevenue,
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
            borderWidth: 1
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₹' + value.toLocaleString();
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

    // Menu Engineering Matrix (Scatter Plot)
    const scatterData = itemNames.map((name, i) => ({
      x: itemQty[i],
      y: itemRevenue[i],
      label: name
    }));

    new Chart(document.getElementById('matrixChart'), {
      type: 'scatter',
      data: {
        datasets: [{
          label: 'Menu Items',
          data: scatterData,
          backgroundColor: '#7E892B',
          borderColor: '#630116',
          borderWidth: 2,
          pointRadius: 8,
          pointHoverRadius: 12
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
            callbacks: {
              label: function(context) {
                return context.raw.label + ' - Qty: ' + context.raw.x + ', Rev: ₹' + context.raw.y.toFixed(2);
              }
            }
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: 'Popularity (Units Sold)',
              font: { size: 13, weight: '600' }
            },
            ticks: {
              font: { size: 12 }
            },
            grid: {
              color: '#f5f3ef'
            }
          },
          y: {
            title: {
              display: true,
              text: 'Revenue (₹)',
              font: { size: 13, weight: '600' }
            },
            ticks: {
              callback: function(value) {
                return '₹' + value.toLocaleString();
              },
              font: { size: 12 }
            },
            grid: {
              color: '#f5f3ef'
            }
          }
        }
      }
    });

    // Items to Promote (Pie Chart - Top 3 by revenue)
    const promoteLabels = itemNames.slice(0, 3);
    const promoteData = itemRevenue.slice(0, 3);

    new Chart(document.getElementById('promoteChart'), {
      type: 'pie',
      data: {
        labels: promoteLabels,
        datasets: [{
          data: promoteData,
          backgroundColor: ['#630116', '#AF5B73', '#7E892B'],
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
            callbacks: {
              label: function(context) {
                return context.label + ': ₹' + context.parsed.toFixed(2);
              }
            }
          }
        }
      }
    });

    // Items to Drop/Rework (Doughnut Chart - Bottom 3 by revenue)
    const dropLabels = itemNames.slice(-3);
    const dropData = itemRevenue.slice(-3);

    new Chart(document.getElementById('dropChart'), {
      type: 'doughnut',
      data: {
        labels: dropLabels,
        datasets: [{
          data: dropData,
          backgroundColor: ['#CEC3C1', '#AF5B73', '#630116'],
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
            callbacks: {
              label: function(context) {
                return context.label + ': ₹' + context.parsed.toFixed(2);
              }
            }
          }
        },
        cutout: '60%'
      }
    });
  </script>

</body>
</html>
<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>
  <link rel="stylesheet" href="static/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    /* ─── variables ─── */
    :root {
      --bg-primary:       #FBF9F5;
      --bg-secondary:     #CEC3C1;
      --accent-primary:   #630116;
      --accent-secondary: #AF5B73;
      --accent-success:   #7E892B;
      --text-dark:        #2a2a2a;
      --text-muted:       #6a6a6a;
    }

    /* ─── layout ─── */
    .content-wrapper {
      margin-left: 260px;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }
    .main-content {
      padding: 48px 40px;
      max-width: 780px;            /* comfortable reading width for settings */
    }

    /* ─── page header ─── */
    .page-header {
      margin-bottom: 48px;
      animation: fadeIn 0.5s ease-out;
    }
    .page-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 38px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.5px;
      line-height: 1.15;
    }
    .page-header p {
      margin-top: 8px;
      font-size: 15px;
      color: var(--text-muted);
      font-family: 'Work Sans', sans-serif;
    }

    /* ─── section card ─── */
    .settings-section {
      background: #fff;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      margin-bottom: 24px;
      box-shadow: 0 2px 8px rgba(99,1,22,0.04);
      transition: box-shadow 0.3s ease;
      animation: fadeIn 0.5s ease-out backwards;
    }
    .settings-section:hover {
      box-shadow: 0 8px 24px rgba(99,1,22,0.12);
    }

    /* stagger each section */
    .settings-section:nth-child(1) { animation-delay: 0.05s; }
    .settings-section:nth-child(2) { animation-delay: 0.10s; }
    .settings-section:nth-child(3) { animation-delay: 0.15s; }
    .settings-section:nth-child(4) { animation-delay: 0.20s; }
    .settings-section:nth-child(5) { animation-delay: 0.25s; }
    .settings-section:nth-child(6) { animation-delay: 0.30s; }

    /* ─── section heading ─── */
    .settings-section h2 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.3px;
      margin-bottom: 24px;
    }

    /* ─── row (every label is one row) ─── */
    .setting-row {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 10px 0;
      border-bottom: 1px solid var(--bg-secondary);
    }
    .setting-row:last-child {
      border-bottom: none;
    }

    .setting-row .row-label {
      font-family: 'Work Sans', sans-serif;
      font-size: 15px;
      font-weight: 400;
      color: var(--text-dark);
      flex: 1;
    }

    /* ─── toggle switch ─── */
    .switch {
      position: relative;
      display: inline-block;
      width: 48px;
      height: 26px;
      flex-shrink: 0;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      inset: 0;
      background: var(--bg-secondary);
      border-radius: 26px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .slider:before {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      left: 3px;
      bottom: 3px;
      background: #fff;
      border-radius: 50%;
      transition: transform 0.3s ease;
      box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .switch input:checked + .slider {
      background: var(--accent-primary);
    }
    .switch input:checked + .slider:before {
      transform: translateX(22px);
    }
    /* focus ring on the hidden checkbox, shown on slider */
    .switch input:focus + .slider {
      box-shadow: 0 0 0 3px rgba(175,91,115,0.2);
    }

    /* ─── select ─── */
    .setting-select {
      appearance: none;
      -webkit-appearance: none;
      padding: 10px 36px 10px 14px;
      font-size: 15px;
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236a6a6a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 12px center;
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      color: var(--text-dark);
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 180px;
    }
    .setting-select:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
    }

    /* ─── password input ─── */
    .setting-input {
      padding: 10px 14px;
      font-size: 15px;
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      color: var(--text-dark);
      transition: all 0.2s ease;
      width: 220px;
    }
    .setting-input::placeholder { color: #999; }
    .setting-input:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
    }

    /* ─── apply button ─── */
    .apply-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 8px;
      padding: 14px 32px;
      font-size: 14px;
      font-weight: 600;
      font-family: 'Work Sans', sans-serif;
      background: var(--accent-primary);
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s ease;
      animation: fadeIn 0.5s ease-out 0.35s backwards;
    }
    .apply-btn:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99,1,22,0.25);
    }
    .apply-btn:active {
      transform: translateY(0);
      box-shadow: none;
    }

    /* ─── toast (replaces alert()) ─── */
    .toast {
      position: fixed;
      bottom: 32px;
      right: 40px;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 20px;
      background: #fff;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(99,1,22,0.12);
      font-family: 'Work Sans', sans-serif;
      font-size: 14px;
      color: var(--text-dark);
      z-index: 999;
      animation: toastIn 0.35s cubic-bezier(.22,.68,0,1.2) forwards;
    }
    .toast i {
      color: var(--accent-success);
      font-size: 16px;
    }
    .toast.hiding {
      animation: toastOut 0.25s ease forwards;
    }

    /* ─── dark mode overrides ─── */
    body.dark-mode {
      --bg-primary:   #111;
      --bg-secondary: #2a2a2a;
      --text-dark:    #eee;
      --text-muted:   #999;
    }
    body.dark-mode .content-wrapper { background: var(--bg-primary); }
    body.dark-mode .settings-section { background: #1c1c1c; }
    body.dark-mode .setting-select,
    body.dark-mode .setting-input   { background: #1c1c1c; color: #eee; border-color: #333; }

    /* large font */
    body.large-font { font-size: 1.15em; }

    /* ─── animations ─── */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes toastIn {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes toastOut {
      from { opacity: 1; transform: translateY(0); }
      to   { opacity: 0; transform: translateY(12px); }
    }

    /* ─── responsive ─── */
    @media (max-width: 768px) {
      .content-wrapper { margin-left: 72px; }
      .main-content    { padding: 32px 24px; }
      .page-header h1  { font-size: 30px; }
    }
    @media (max-width: 480px) {
      .content-wrapper  { margin-left: 0; }
      .main-content     { padding: 24px 16px; }
      .page-header h1   { font-size: 28px; }
      .settings-section { padding: 24px 16px; }
      .setting-select,
      .setting-input    { width: 100%; min-width: 0; }
      .setting-row      { flex-wrap: wrap; }
    }
  </style>
</head>
<body>

  <?php include '../includes/sidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <div class="page-header">
        <h1>Settings</h1>
        <p>Manage dashboard appearance, notifications, POS, inventory, and user preferences</p>
      </div>

      <!-- Appearance -->
      <section class="settings-section">
        <h2>Appearance</h2>

        <div class="setting-row">
          <span class="row-label">Enable Dark Mode</span>
          <span class="switch">
            <input type="checkbox" id="darkModeToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Large Font Mode</span>
          <span class="switch">
            <input type="checkbox" id="largeFontToggle">
            <span class="slider"></span>
          </span>
        </div>
      </section>

      <!-- Notifications & Dashboard -->
      <section class="settings-section">
        <h2>Notifications &amp; Dashboard</h2>

        <div class="setting-row">
          <span class="row-label">Enable Notifications</span>
          <span class="switch">
            <input type="checkbox" id="notificationsToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Sound Alerts for Orders</span>
          <span class="switch">
            <input type="checkbox" id="soundToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Auto-refresh Dashboard Pages</span>
          <span class="switch">
            <input type="checkbox" id="autoRefreshToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Default Landing Page</span>
          <select class="setting-select" id="landingPage">
            <option value="home.php">Home</option>
            <option value="pos.php">Point of Sale</option>
            <option value="forecast.php">Demand Forecast</option>
          </select>
        </div>
      </section>

      <!-- Order & POS -->
      <section class="settings-section">
        <h2>Order &amp; POS</h2>

        <div class="setting-row">
          <span class="row-label">Default Payment Method</span>
          <select class="setting-select" id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="upi">UPI</option>
          </select>
        </div>

        <div class="setting-row">
          <span class="row-label">Auto-generate Invoice</span>
          <span class="switch">
            <input type="checkbox" id="autoInvoiceToggle">
            <span class="slider"></span>
          </span>
        </div>
      </section>

      <!-- Inventory -->
      <section class="settings-section">
        <h2>Inventory</h2>

        <div class="setting-row">
          <span class="row-label">Low Stock Alerts</span>
          <span class="switch">
            <input type="checkbox" id="lowStockAlertToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Auto-deduct Stock on POS</span>
          <span class="switch">
            <input type="checkbox" id="autoDeductStockToggle">
            <span class="slider"></span>
          </span>
        </div>
      </section>

      <!-- User & Security -->
      <section class="settings-section">
        <h2>User &amp; Security</h2>

        <div class="setting-row">
          <span class="row-label">Enable Session Timeout</span>
          <span class="switch">
            <input type="checkbox" id="sessionTimeoutToggle">
            <span class="slider"></span>
          </span>
        </div>

        <div class="setting-row">
          <span class="row-label">Change Password</span>
          <input type="password" class="setting-input" id="newPassword" placeholder="New password">
        </div>
      </section>

      <!-- Miscellaneous -->
      <section class="settings-section">
        <h2>Miscellaneous</h2>

        <div class="setting-row">
          <span class="row-label">Language</span>
          <select class="setting-select" id="languageSelect">
            <option value="en">English</option>
            <option value="hi">Hindi</option>
          </select>
        </div>

        <div class="setting-row">
          <span class="row-label">Time Format</span>
          <select class="setting-select" id="timeFormatSelect">
            <option value="12">12-hour</option>
            <option value="24">24-hour</option>
          </select>
        </div>

        <div class="setting-row">
          <span class="row-label">Currency</span>
          <select class="setting-select" id="currencySelect">
            <option value="₹">INR (₹)</option>
            <option value="$">USD ($)</option>
          </select>
        </div>
      </section>

      <button class="apply-btn" id="applySettings">
        <i class="fas fa-check"></i> Apply Settings
      </button>

    </main>
  </div>

  <!-- toast container (JS driven) -->
  <div id="toast-wrap"></div>

  <script>
    /* ────────────────────────────────────────────
       SAME LOGIC AS BEFORE — nothing changed
       ──────────────────────────────────────────── */
    const settings = [
      { id: 'darkModeToggle',        key: 'darkMode',        type: 'class', className: 'dark-mode' },
      { id: 'largeFontToggle',       key: 'largeFont',       type: 'class', className: 'large-font' },
      { id: 'notificationsToggle',   key: 'notifications' },
      { id: 'soundToggle',           key: 'sound' },
      { id: 'autoRefreshToggle',     key: 'autoRefresh' },
      { id: 'autoInvoiceToggle',     key: 'autoInvoice' },
      { id: 'lowStockAlertToggle',   key: 'lowStockAlert' },
      { id: 'autoDeductStockToggle', key: 'autoDeductStock' },
      { id: 'sessionTimeoutToggle',  key: 'sessionTimeout' }
    ];

    const dropdowns = [
      { id: 'landingPage',      key: 'landingPage' },
      { id: 'paymentMethod',    key: 'paymentMethod' },
      { id: 'languageSelect',   key: 'language' },
      { id: 'timeFormatSelect', key: 'timeFormat' },
      { id: 'currencySelect',   key: 'currency' }
    ];

    // Load saved
    settings.forEach(function(s) {
      var el = document.getElementById(s.id);
      if (localStorage.getItem(s.key) === 'true') el.checked = true;
      if (s.type === 'class' && el.checked) document.body.classList.add(s.className);
    });

    dropdowns.forEach(function(d) {
      var el = document.getElementById(d.id);
      var saved = localStorage.getItem(d.key);
      if (saved) el.value = saved;
    });

    // Apply
    document.getElementById('applySettings').addEventListener('click', function() {
      settings.forEach(function(s) {
        var el = document.getElementById(s.id);
        localStorage.setItem(s.key, el.checked);
        if (s.type === 'class') {
          if (el.checked) document.body.classList.add(s.className);
          else             document.body.classList.remove(s.className);
        }
      });

      dropdowns.forEach(function(d) {
        localStorage.setItem(d.key, document.getElementById(d.id).value);
      });

      showToast('Settings saved successfully');
    });

    /* ── toast helper (replaces alert) ── */
    var toastTimer = null;
    function showToast(msg) {
      if (toastTimer) clearTimeout(toastTimer);
      var wrap = document.getElementById('toast-wrap');
      wrap.innerHTML =
        '<div class="toast">' +
          '<i class="fas fa-circle-check"></i>' +
          '<span>' + msg + '</span>' +
        '</div>';
      var toast = wrap.querySelector('.toast');
      toastTimer = setTimeout(function() {
        toast.classList.add('hiding');
        setTimeout(function() { wrap.innerHTML = ''; }, 260);
      }, 2800);
    }
  </script>

</body>
</html>
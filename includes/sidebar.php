<?php
$current_page = basename($_SERVER['PHP_SELF']);
$in_pages = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'pages');
$base = $in_pages ? '../' : '';
?>
<aside class="sidebar">
  <div class="logo-section">
    <svg class="logo-icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
      <rect x="4" y="4" width="24" height="24" rx="4" fill="#630116"/>
      <path d="M12 16L14.5 18.5L20 13" stroke="#FBF9F5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    </svg>
    <span class="logo-text">Cloud Kitchen</span>
  </div>
  
  <nav class="nav-container">
    <a href="<?= $base ?>home.php" class="nav-link <?= $current_page == 'home.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4v-7h-6v7H5a2 2 0 0 1-2-2z"/>
      </svg>
      <span class="nav-text">Home</span>
    </a>
    
    <a href="<?= $base ?>pages/forecast.php" class="nav-link <?= $current_page == 'forecast.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h4v12H4zM10 10h4v8h-4zM16 2h4v16h-4z"/>
      </svg>
      <span class="nav-text">Demand Forecast</span>
    </a>
    
    <a href="<?= $base ?>pages/menu-optimization.php" class="nav-link <?= $current_page == 'menu-optimization.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="21" x2="4" y2="14"/>
        <line x1="4" y1="10" x2="4" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="12"/>
        <line x1="12" y1="8" x2="12" y2="3"/>
        <line x1="20" y1="21" x2="20" y2="7"/>
        <line x1="20" y1="3" x2="20" y2="3"/>
      </svg>
      <span class="nav-text">Menu Optimization</span>
    </a>
    
    <a href="<?= $base ?>pages/history.php" class="nav-link <?= $current_page == 'history.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
      </svg>
      <span class="nav-text">Order History</span>
    </a>
    
    <a href="<?= $base ?>pages/sales-report.php" class="nav-link <?= $current_page == 'sales-report.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 9 12 15 6 21 12"/>
      </svg>
      <span class="nav-text">Sales Report</span>
    </a>
    
    <a href="<?= $base ?>pages/customer-feedback.php" class="nav-link <?= $current_page == 'customer-feedback.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"/>
      </svg>
      <span class="nav-text">Customer Feedback</span>
    </a>
    
    <a href="<?= $base ?>pages/user-management.php" class="nav-link <?= $current_page == 'user-management.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      </svg>
      <span class="nav-text">User Management</span>
    </a>
    
    <a href="<?= $base ?>pages/pos.php" class="nav-link <?= $current_page == 'pos.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
        <line x1="2" y1="11" x2="22" y2="11"/>
      </svg>
      <span class="nav-text">Point of Sale</span>
    </a>
    
    <a href="<?= $base ?>pages/kitchen.php" class="nav-link <?= $current_page == 'kitchen.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 3h18v18H3z"/>
      </svg>
      <span class="nav-text">Kitchen Orders</span>
    </a>
    
    <a href="<?= $base ?>pages/update-menu.php" class="nav-link <?= $current_page == 'update-menu.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"/>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
      </svg>
      <span class="nav-text">Update Menu</span>
    </a>
    
    <a href="<?= $base ?>pages/settings.php" class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
      </svg>
      <span class="nav-text">Settings</span>
    </a>
  </nav>
  
  <div class="sidebar-footer">
    <p class="footer-text">&copy; <?= date('Y'); ?> Cloud Kitchen</p>
  </div>
</aside>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@400;500;600&display=swap');

  /* CSS Variables */
  :root {
    --bg-primary: #FBF9F5;
    --bg-secondary: #CEC3C1;
    --accent-primary: #630116;
    --accent-secondary: #AF5B73;
    --accent-success: #7E892B;
    --text-dark: #2a2a2a;
    --text-muted: #6a6a6a;
  }

  /* Sidebar Container */
  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: var(--bg-primary);
    border-right: 1px solid var(--bg-secondary);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    z-index: 100;
    transition: all 0.3s ease;
  }

  /* Logo Section */
  .logo-section {
    padding: 32px 24px;
    border-bottom: 1px solid var(--bg-secondary);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
  }

  .logo-text {
    font-family: 'Crimson Pro', serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--accent-primary);
    letter-spacing: -0.3px;
  }

  /* Navigation Container */
  .nav-container {
    flex: 1;
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  /* Navigation Links */
  .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--text-dark);
    text-decoration: none;
    font-family: 'Work Sans', sans-serif;
    font-size: 15px;
    font-weight: 400;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  .nav-link svg {
    flex-shrink: 0;
    opacity: 0.7;
    transition: all 0.2s ease;
  }

  .nav-text {
    flex: 1;
  }

  /* Navigation Hover State */
  .nav-link:hover {
    background: var(--bg-secondary);
    color: var(--accent-primary);
  }

  .nav-link:hover svg {
    opacity: 1;
  }

  /* Navigation Active State */
  .nav-link.active {
    background: var(--accent-primary);
    color: var(--bg-primary);
  }

  .nav-link.active svg {
    stroke: var(--bg-primary);
    opacity: 1;
  }

  /* Sidebar Footer */
  .sidebar-footer {
    padding: 24px;
    border-top: 1px solid var(--bg-secondary);
  }

  .footer-text {
    font-family: 'Work Sans', sans-serif;
    font-size: 12px;
    color: var(--text-muted);
    text-align: center;
  }

  /* Scrollbar Styling */
  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-track {
    background: var(--bg-primary);
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: var(--bg-secondary);
    border-radius: 3px;
  }

  .sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--accent-secondary);
  }

  /* Tablet - Icon Only Sidebar */
  @media (max-width: 768px) {
    .sidebar {
      width: 72px;
    }
    
    .logo-section {
      padding: 24px 16px;
      justify-content: center;
    }
    
    .logo-text,
    .nav-text,
    .footer-text {
      display: none;
    }
    
    .nav-container {
      padding: 24px 12px;
    }
    
    .nav-link {
      justify-content: center;
      padding: 12px;
    }
  }

  /* Mobile - Hide Sidebar */
  @media (max-width: 480px) {
    .sidebar {
      transform: translateX(-100%);
    }
    
    .sidebar.open {
      transform: translateX(0);
    }
  }
</style>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
$in_pages = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'pages');
$base = $in_pages ? '../' : '';
?>
<aside class="sidebar">
  <div class="logo">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="4" y="4" width="24" height="24" rx="4" fill="#630116"/>
      <path d="M12 16L14.5 18.5L20 13" stroke="#FBF9F5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>Point of Sale</span>
  </div>
  
  <nav>
    <ul class="nav-links">
      <li>
        <a href="<?= $base ?>index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          <span>Login</span>
        </a>
      </li>
      <li>
        <a href="<?= $base ?>pages/contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
          <span>Contact Us</span>
        </a>
      </li>
      <li>
        <a href="<?= $base ?>pages/developers.php" class="<?= $current_page == 'developers.php' ? 'active' : '' ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="16 18 22 12 16 6"/>
            <polyline points="8 6 2 12 8 18"/>
          </svg>
          <span>Developers</span>
        </a>
      </li>
      <li>
        <a href="<?= $base ?>pages/about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
          <span>About</span>
        </a>
      </li>
    </ul>
  </nav>

  <div class="sidebar-footer">
    <p>&copy; <?= date('Y'); ?> POS</p>
  </div>
</aside>

<style>
  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: #FBF9F5;
    border-right: 1px solid #CEC3C1;
    display: flex;
    flex-direction: column;
    z-index: 100;
  }

  .logo {
    padding: 32px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #CEC3C1;
  }

  .logo span {
    font-family: 'Crimson Pro', serif;
    font-size: 18px;
    font-weight: 600;
    color: #630116;
    letter-spacing: -0.3px;
  }

  .nav-links {
    list-style: none;
    padding: 24px 16px;
    flex: 1;
  }

  .nav-links li {
    margin-bottom: 4px;
  }

  .nav-links a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #2a2a2a;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .nav-links a svg {
    flex-shrink: 0;
    opacity: 0.7;
    transition: opacity 0.2s ease;
  }

  .nav-links a:hover {
    background: #CEC3C1;
    color: #630116;
  }

  .nav-links a:hover svg {
    opacity: 1;
  }

  .nav-links a.active {
    background: #630116;
    color: #FBF9F5;
  }

  .nav-links a.active svg {
    opacity: 1;
    stroke: #FBF9F5;
  }

  .sidebar-footer {
    padding: 24px;
    border-top: 1px solid #CEC3C1;
    text-align: center;
  }

  .sidebar-footer p {
    font-size: 12px;
    color: #6a6a6a;
    font-weight: 400;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .sidebar {
      width: 72px;
    }

    .logo span,
    .nav-links a span,
    .sidebar-footer p {
      display: none;
    }

    .logo {
      justify-content: center;
      padding: 24px 16px;
    }

    .nav-links {
      padding: 24px 12px;
    }

    .nav-links a {
      justify-content: center;
      padding: 12px;
    }
  }
</style>
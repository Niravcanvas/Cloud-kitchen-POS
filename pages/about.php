<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About - POS System</title>
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

    .content-wrapper {
      margin-left: 260px;
      min-height: 100vh;
      padding: 48px 40px;
      transition: margin-left 0.3s ease;
    }

    .main-content {
      max-width: 900px;
      margin: 0 auto;
    }

    /* Page Header */
    .page-header {
      margin-bottom: 64px;
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
      font-size: 48px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 16px;
      letter-spacing: -0.5px;
      line-height: 1.1;
    }

    .page-header p {
      font-size: 18px;
      color: var(--text-muted);
      line-height: 1.6;
      max-width: 600px;
    }

    /* Overview Section */
    .overview-section {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 48px;
      margin-bottom: 48px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.1s backwards;
    }

    .overview-section p {
      font-size: 16px;
      line-height: 1.8;
      color: var(--text-dark);
    }

    /* Features Grid */
    .features-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 48px;
      animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .feature-item {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 24px;
      transition: all 0.2s ease;
    }

    .feature-item:hover {
      border-color: var(--accent-secondary);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.08);
    }

    .feature-icon {
      width: 40px;
      height: 40px;
      background: var(--bg-primary);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }

    .feature-icon svg {
      width: 20px;
      height: 20px;
      stroke: var(--accent-primary);
    }

    .feature-item h4 {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .feature-item p {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* Mission/Vision Section */
    .values-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      animation: fadeIn 0.6s ease-out 0.3s backwards;
    }

    .value-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 40px 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
    }

    .value-card h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 16px;
      letter-spacing: -0.3px;
    }

    .value-card p {
      font-size: 15px;
      line-height: 1.7;
      color: var(--text-dark);
    }

    /* Section Heading */
    .section-heading {
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--text-muted);
      font-weight: 600;
      margin-bottom: 24px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--bg-secondary);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 72px;
        padding: 32px 24px;
      }

      .features-grid,
      .values-section {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .page-header h1 {
        font-size: 36px;
      }

      .overview-section {
        padding: 32px 24px;
      }
    }

    @media (max-width: 480px) {
      .content-wrapper {
        margin-left: 0;
        padding: 24px 16px;
      }

      .page-header h1 {
        font-size: 32px;
      }

      .page-header {
        margin-bottom: 40px;
      }

      .overview-section,
      .feature-item,
      .value-card {
        padding: 24px 20px;
      }
    }
  </style>
</head>
<body>

  <?php include '../includes/Lsidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <div class="page-header">
        <h1>About POS System</h1>
        <p>Complete management solution for cloud kitchens and restaurants.</p>
      </div>

      <!-- Overview -->
      <div class="overview-section">
        <p>
          POS System is an integrated management platform designed for cloud kitchens and restaurants. 
          It streamlines operations, reduces waste, tracks sales, and improves customer satisfaction 
          by combining analytics, order tracking, and performance monitoring in one place.
        </p>
      </div>

      <!-- Features -->
      <h2 class="section-heading">Key Features</h2>
      <div class="features-grid">
        
        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
          </div>
          <h4>Dashboard</h4>
          <p>Overview of operations with quick access to all modules</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
          </div>
          <h4>Sales Analytics</h4>
          <p>Detailed insights on revenue, trends, and top-selling items</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
          </div>
          <h4>Menu Management</h4>
          <p>Optimize menu based on performance and customer preferences</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
          </div>
          <h4>Order Tracking</h4>
          <p>Real-time kitchen orders and complete order history</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
            </svg>
          </div>
          <h4>Point of Sale</h4>
          <p>Process orders and payments seamlessly</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
          </div>
          <h4>User Management</h4>
          <p>Role-based access control for staff and admins</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
            </svg>
          </div>
          <h4>Demand Forecasting</h4>
          <p>Predict trends to optimize inventory and reduce waste</p>
        </div>

        <div class="feature-item">
          <div class="feature-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
          </div>
          <h4>Customer Feedback</h4>
          <p>Collect and analyze feedback to improve quality</p>
        </div>

      </div>

      <!-- Mission & Vision -->
      <h2 class="section-heading">Our Purpose</h2>
      <div class="values-section">
        
        <div class="value-card">
          <h3>Mission</h3>
          <p>
            Empower food businesses with data-driven insights and tools to reduce costs, 
            increase efficiency, and deliver exceptional customer experiences.
          </p>
        </div>

        <div class="value-card">
          <h3>Vision</h3>
          <p>
            Become the leading platform for restaurant management worldwide, 
            making food businesses smarter, more profitable, and sustainable.
          </p>
        </div>

      </div>

    </main>
  </div>

</body>
</html>
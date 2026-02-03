<?php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - CakeCafe POS</title>
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

    /* Main content area */
    .content-wrapper {
      margin-left: 260px;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      transition: margin-left 0.3s ease;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
      animation: fadeIn 0.6s ease-out;
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

    .login-header {
      text-align: center;
      margin-bottom: 48px;
    }

    .login-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 36px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .login-header p {
      font-size: 15px;
      color: var(--text-muted);
      font-weight: 400;
    }

    .login-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 48px 40px;
      box-shadow: 0 2px 12px rgba(99, 1, 22, 0.06);
    }

    .error-message {
      background: #fff5f5;
      border: 1px solid var(--accent-primary);
      color: var(--accent-primary);
      padding: 14px 18px;
      border-radius: 6px;
      margin-bottom: 28px;
      font-size: 14px;
      text-align: center;
      animation: shake 0.3s ease-out;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-8px); }
      75% { transform: translateX(8px); }
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .input-wrapper {
      position: relative;
    }

    .form-group input {
      width: 100%;
      padding: 14px 16px;
      font-size: 15px;
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: 6px;
      color: var(--text-dark);
      transition: all 0.2s ease;
    }

    .form-group input::placeholder {
      color: #999;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175, 91, 115, 0.1);
    }

    .login-btn {
      width: 100%;
      padding: 16px;
      font-size: 15px;
      font-weight: 600;
      font-family: 'Work Sans', sans-serif;
      background: var(--accent-primary);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .login-btn:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.25);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 72px;
      }

      .login-card {
        padding: 36px 28px;
      }

      .login-header h1 {
        font-size: 30px;
      }
    }

    @media (max-width: 480px) {
      .content-wrapper {
        margin-left: 0;
        padding: 16px;
      }

      .login-container {
        max-width: 100%;
      }

      .login-header {
        margin-bottom: 32px;
      }

      .login-card {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>

  <?php include 'includes/Lsidebar.php'; ?>

  <div class="content-wrapper">
    <div class="login-container">
      <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Enter your credentials to continue</p>
      </div>

      <div class="login-card">
        
        <?php if (isset($_SESSION['error'])): ?>
          <div class="error-message">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['error']); ?></span>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="includes/auth.php">
          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrapper">
              <input 
                type="text" 
                id="username"
                name="username" 
                placeholder="Enter your username" 
                required
                autocomplete="username"
                autofocus
              >
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <input 
                type="password" 
                id="password"
                name="password" 
                placeholder="Enter your password" 
                required
                autocomplete="current-password"
              >
            </div>
          </div>

          <button type="submit" class="login-btn">Login</button>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
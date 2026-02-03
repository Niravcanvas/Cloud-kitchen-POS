<?php
session_start();

// Show errors (DEV only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load .env file
$env = parse_ini_file(__DIR__ . '/../.env');
if (!$env) {
    die('ENV file not loaded');
}

// Load Composer autoloader (IMPORTANT)
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Basic sanitization
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = nl2br(htmlspecialchars(trim($_POST['message'])));

    if (!$email) {
        $_SESSION['success'] = 'Invalid email address.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP CONFIG (Gmail example)
        $mail->isSMTP();
        $mail->Host       = $env['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['SMTP_USER'];
        $mail->Password   = $env['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)$env['SMTP_PORT'];

        // Mail headers
        $mail->setFrom($env['SMTP_USER'], 'CakeCafe POS');
        $mail->addReplyTo($email, $name);
        $mail->addAddress($env['SMTP_USER']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "[Contact] $subject";
        $mail->Body = "
            <strong>Name:</strong> {$name}<br>
            <strong>Email:</strong> {$email}<br><br>
            <strong>Message:</strong><br>
            {$message}
        ";

        $mail->send();

        $_SESSION['success'] = 'Your message has been sent successfully!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    } catch (Exception $e) {
        $_SESSION['success'] = 'Message could not be sent. Try again later.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - POS System</title>
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
      max-width: 1000px;
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

    /* Contact Grid */
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      margin-bottom: 48px;
    }

    /* Contact Form Card */
    .contact-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.1s backwards;
    }

    .contact-card h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 24px;
      letter-spacing: -0.3px;
    }

    /* Form Styling */
    .form-group {
      margin-bottom: 20px;
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

    input, textarea {
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

    input::placeholder,
    textarea::placeholder {
      color: #999;
    }

    input:focus,
    textarea:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175, 91, 115, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    .btn-primary {
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
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 8px;
    }

    .btn-primary:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.25);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    /* Contact Info Card */
    .info-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .info-card h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 24px;
      letter-spacing: -0.3px;
    }

    .contact-method {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 20px;
      background: var(--bg-primary);
      border-radius: 6px;
      margin-bottom: 16px;
      transition: all 0.2s ease;
    }

    .contact-method:last-child {
      margin-bottom: 0;
    }

    .contact-method:hover {
      background: #f5f3ef;
      transform: translateX(4px);
    }

    .contact-icon {
      width: 44px;
      height: 44px;
      background: var(--accent-primary);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .contact-icon svg {
      width: 20px;
      height: 20px;
      stroke: white;
    }

    .contact-details {
      flex: 1;
    }

    .contact-label {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      margin-bottom: 4px;
      font-weight: 500;
    }

    .contact-value {
      font-size: 15px;
      color: var(--text-dark);
      font-weight: 500;
    }

    .contact-value a {
      color: var(--accent-secondary);
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .contact-value a:hover {
      color: var(--accent-primary);
    }

    /* Success Message */
    .success-message {
      background: #f0f4e8;
      border: 1px solid var(--accent-success);
      color: var(--accent-success);
      padding: 14px 18px;
      border-radius: 6px;
      margin-bottom: 24px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 72px;
        padding: 32px 24px;
      }

      .contact-grid {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .page-header h1 {
        font-size: 32px;
      }
    }

    @media (max-width: 480px) {
      .content-wrapper {
        margin-left: 0;
        padding: 24px 16px;
      }

      .contact-card,
      .info-card {
        padding: 24px 20px;
      }

      .page-header h1 {
        font-size: 28px;
      }

      .contact-method {
        padding: 16px;
      }
    }
  </style>
</head>
<body>

  <?php include '../includes/Lsidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">
      
      <div class="page-header">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Fill out the form below or reach us directly.</p>
      </div>

      <div class="contact-grid">
        
        <!-- Contact Form -->
        <div class="contact-card">
          <h3>Send a Message</h3>
          
          <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span><?= htmlspecialchars($_SESSION['success']); ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label for="name">Name</label>
              <input 
                type="text" 
                id="name"
                name="name" 
                placeholder="Enter your name" 
                required
              >
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input 
                type="email" 
                id="email"
                name="email" 
                placeholder="Enter your email" 
                required
              >
            </div>

            <div class="form-group">
              <label for="subject">Subject</label>
              <input 
                type="text" 
                id="subject"
                name="subject" 
                placeholder="What's this about?" 
                required
              >
            </div>

            <div class="form-group">
              <label for="message">Message</label>
              <textarea 
                id="message"
                name="message" 
                placeholder="Tell us more..." 
                required
              ></textarea>
            </div>

            <button type="submit" class="btn-primary">Send Message</button>
          </form>
        </div>

        <!-- Contact Information -->
        <div class="info-card">
          <h3>Get in Touch</h3>
          
          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Email</div>
              <div class="contact-value">
                <a href="mailto:support@possystem.com">support@possystem.com</a>
              </div>
            </div>
          </div>

          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Phone</div>
              <div class="contact-value">
                <a href="tel:+919876543210">+91 98765 43210</a>
              </div>
            </div>
          </div>

          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Address</div>
              <div class="contact-value">123 Cloud Street, Tech City, India</div>
            </div>
          </div>

        </div>

      </div>

    </main>
  </div>

</body>
</html>
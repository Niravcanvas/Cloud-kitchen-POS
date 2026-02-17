<?php
session_start();

// Show errors (DEV only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$env = [
    'SMTP_HOST' => getenv('SMTP_HOST'),
    'SMTP_USER' => getenv('SMTP_USER'),
    'SMTP_PASS' => getenv('SMTP_PASS'),
    'SMTP_PORT' => getenv('SMTP_PORT') ?: '587',
];

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = nl2br(htmlspecialchars(trim($_POST['message'])));
    $date    = date('d M Y');

    if (!$email) {
        $_SESSION['success'] = 'Invalid email address.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // ── Styled HTML email body (matches invoice.php design) ──
    $emailBody = '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Contact Message</title>
  <style>
    body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
    table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}
    body{margin:0!important;padding:0!important;width:100%!important}
  </style>
</head>
<body style="margin:0;padding:0;background-color:#EDEBE7;font-family:Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#EDEBE7;">
  <tr>
    <td align="center" style="padding:32px 16px 40px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
             style="width:600px;background-color:#FFFFFF;border:1px solid #CEC3C1;border-radius:3px;
                    box-shadow:0 4px 24px rgba(99,1,22,0.08);">

        <!-- HEADER -->
        <tr>
          <td style="background-color:#FBF9F5;border-bottom:1px solid #CEC3C1;padding:24px 36px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td valign="middle">
                  <div style="font-family:Georgia,serif;font-size:22px;font-weight:bold;color:#630116;line-height:1;margin-bottom:4px;">Point of Sale</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:10px;color:#6a6a6a;letter-spacing:0.12em;text-transform:uppercase;">Cloud Kitchen POS System</div>
                </td>
                <td valign="middle" align="right">
                  <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                    <td style="background-color:#630116;border-radius:2px;padding:5px 14px;">
                      <span style="font-family:Helvetica,Arial,sans-serif;font-size:11px;font-weight:bold;color:#FFFFFF;letter-spacing:0.12em;text-transform:uppercase;">CONTACT MESSAGE</span>
                    </td>
                  </tr></table>
                  <div style="margin-top:6px;font-size:10px;color:#6a6a6a;font-family:Helvetica,Arial,sans-serif;text-align:right;">' . $date . '</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FROM -->
        <tr>
          <td style="padding:28px 36px 0;">
            <div style="font-family:Helvetica,Arial,sans-serif;font-size:9px;font-weight:bold;color:#630116;letter-spacing:0.2em;text-transform:uppercase;margin-bottom:4px;">MESSAGE FROM</div>
            <div style="font-family:Georgia,serif;font-size:20px;font-weight:bold;color:#2a2a2a;margin-bottom:2px;">' . $name . '</div>
            <div style="font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#6a6a6a;">' . $email . '</div>
          </td>
        </tr>

        <!-- DIVIDER -->
        <tr>
          <td style="padding:16px 36px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr><td style="border-top:1px solid #CEC3C1;font-size:0;line-height:0;">&nbsp;</td></tr>
            </table>
          </td>
        </tr>

        <!-- SUBJECT -->
        <tr>
          <td style="padding:16px 36px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td width="110" style="background-color:#630116;padding:8px 14px;border-radius:2px 0 0 2px;">
                  <span style="font-family:Helvetica,Arial,sans-serif;font-size:9px;font-weight:bold;color:#FFFFFF;letter-spacing:0.18em;text-transform:uppercase;">SUBJECT</span>
                </td>
                <td style="background-color:#FBF9F5;padding:8px 14px;border:1px solid #CEC3C1;border-left:none;border-radius:0 2px 2px 0;">
                  <span style="font-family:Helvetica,Arial,sans-serif;font-size:13px;font-weight:bold;color:#2a2a2a;">' . $subject . '</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- MESSAGE -->
        <tr>
          <td style="padding:16px 36px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="background-color:#FFFFFF;border:1px solid #CEC3C1;border-radius:2px;padding:20px 18px;">
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:9px;font-weight:bold;color:#630116;letter-spacing:0.18em;text-transform:uppercase;margin-bottom:10px;">MESSAGE</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#2a2a2a;line-height:1.7;">' . $message . '</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- INFO STRIP -->
        <tr>
          <td style="padding:0 36px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background-color:#FBF9F5;border:1px solid #CEC3C1;border-radius:2px;">
              <tr>
                <td valign="top" width="33%" style="border-right:1px solid #CEC3C1;padding:14px 16px;">
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:8px;font-weight:bold;color:#AF5B73;letter-spacing:0.16em;text-transform:uppercase;margin-bottom:5px;">EMAIL</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#2a2a2a;">rushabpn23hcs@student.mes.ac.in</div>
                </td>
                <td valign="top" width="33%" style="border-right:1px solid #CEC3C1;padding:14px 16px;">
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:8px;font-weight:bold;color:#AF5B73;letter-spacing:0.16em;text-transform:uppercase;margin-bottom:5px;">PHONE</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#2a2a2a;">+91 98765 43210</div>
                </td>
                <td valign="top" width="34%" style="padding:14px 16px;">
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:8px;font-weight:bold;color:#AF5B73;letter-spacing:0.16em;text-transform:uppercase;margin-bottom:5px;">ADDRESS</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#2a2a2a;">123 Cloud Street,<br>Tech City, India</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="border-top:1px solid #CEC3C1;padding:18px 36px 24px;background-color:#FBF9F5;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center">
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:11px;color:#6a6a6a;margin-bottom:4px;">This message was submitted via the CakeCafe POS contact form.</div>
                  <div style="font-family:Helvetica,Arial,sans-serif;font-size:10px;color:#9e9e9e;">123 Cloud Street, Tech City, India &nbsp;|&nbsp; +91 98765 43210</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $env['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['SMTP_USER'];
        $mail->Password   = $env['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)$env['SMTP_PORT'];

        $mail->setFrom($env['SMTP_USER'], 'CakeCafe POS');
        $mail->addReplyTo($email, $name);
        $mail->addAddress('rushabpn23hcs@student.mes.ac.in');

        $mail->isHTML(true);
        $mail->Subject = "[Contact] $subject";
        $mail->Body    = $emailBody;
        $mail->AltBody = "From: {$name} ({$email})\nSubject: {$subject}\n\n" . strip_tags(str_replace('<br>', "\n", $message));

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
    * { margin: 0; padding: 0; box-sizing: border-box; }

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

    .main-content { max-width: 1000px; margin: 0 auto; }

    .page-header { margin-bottom: 48px; animation: fadeIn 0.5s ease-out; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .page-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 40px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 12px;
      letter-spacing: -0.5px;
    }

    .page-header p { font-size: 16px; color: var(--text-muted); line-height: 1.5; }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      margin-bottom: 48px;
    }

    .contact-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99,1,22,0.04);
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

    .form-group { margin-bottom: 20px; }

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

    input::placeholder, textarea::placeholder { color: #999; }

    input:focus, textarea:focus {
      outline: none;
      border-color: var(--accent-secondary);
      background: white;
      box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
    }

    textarea { resize: vertical; min-height: 120px; }

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
      box-shadow: 0 4px 12px rgba(99,1,22,0.25);
    }

    .btn-primary:active { transform: translateY(0); }

    .info-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(99,1,22,0.04);
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

    .contact-method:last-child { margin-bottom: 0; }

    .contact-method:hover { background: #f5f3ef; transform: translateX(4px); }

    .contact-icon {
      width: 44px; height: 44px;
      background: var(--accent-primary);
      border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .contact-icon svg { width: 20px; height: 20px; stroke: white; }

    .contact-details { flex: 1; }

    .contact-label {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      margin-bottom: 4px;
      font-weight: 500;
    }

    .contact-value { font-size: 15px; color: var(--text-dark); font-weight: 500; }

    .contact-value a {
      color: var(--accent-secondary);
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .contact-value a:hover { color: var(--accent-primary); }

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

    .error-message {
      background: #fdf2f4;
      border: 1px solid var(--accent-secondary);
      color: var(--accent-primary);
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
      from { opacity: 0; transform: translateY(-10px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
      .content-wrapper { margin-left: 72px; padding: 32px 24px; }
      .contact-grid    { grid-template-columns: 1fr; gap: 24px; }
      .page-header h1  { font-size: 32px; }
    }

    @media (max-width: 480px) {
      .content-wrapper { margin-left: 0; padding: 24px 16px; }
      .contact-card, .info-card { padding: 24px 20px; }
      .page-header h1  { font-size: 28px; }
      .contact-method  { padding: 16px; }
    }
  </style>
</head>
<body>

  <?php include '../includes/Lsidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <div class="page-header">
        <h1>Contact Us</h1>
        <p>We\'d love to hear from you! Fill out the form below or reach us directly.</p>
      </div>

      <div class="contact-grid">

        <!-- Contact Form -->
        <div class="contact-card">
          <h3>Send a Message</h3>

          <?php if (isset($_SESSION['success'])): ?>
            <?php $isError = str_contains($_SESSION['success'], 'could not') || str_contains($_SESSION['success'], 'Invalid'); ?>
            <div class="<?= $isError ? 'error-message' : 'success-message' ?>">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <?php if ($isError): ?>
                  <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                <?php else: ?>
                  <polyline points="20 6 9 17 4 12"/>
                <?php endif; ?>
              </svg>
              <span><?= htmlspecialchars($_SESSION['success']); ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject" placeholder="What\'s this about?" required>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea id="message" name="message" placeholder="Tell us more..." required></textarea>
            </div>
            <button type="submit" class="btn-primary">Send Message</button>
          </form>
        </div>

        <!-- Contact Info -->
        <div class="info-card">
          <h3>Get in Touch</h3>

          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Email</div>
              <div class="contact-value"><a href="mailto:rushabpn23hcs@student.mes.ac.in">rushabpn23hcs@student.mes.ac.in</a></div>
            </div>
          </div>

          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Phone</div>
              <div class="contact-value"><a href="tel:+919876543210">+91 98765 43210</a></div>
            </div>
          </div>

          <div class="contact-method">
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
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
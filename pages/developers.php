<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Developers - POS System</title>
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
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Page Header */
    .page-header {
      text-align: center;
      margin-bottom: 56px;
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

    /* Developer Grid */
    .developer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 32px;
      margin-bottom: 48px;
    }

    /* Developer Card */
    .developer-card {
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      padding: 40px 32px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.04);
      transition: all 0.3s ease;
      animation: fadeIn 0.6s ease-out backwards;
    }

    .developer-card:nth-child(1) {
      animation-delay: 0.1s;
    }

    .developer-card:nth-child(2) {
      animation-delay: 0.2s;
    }

    .developer-card:nth-child(3) {
      animation-delay: 0.3s;
    }

    .developer-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(99, 1, 22, 0.12);
      border-color: var(--accent-secondary);
    }

    /* Developer Photo */
    .developer-photo-wrapper {
      width: 120px;
      height: 120px;
      margin: 0 auto 24px;
      position: relative;
    }

    .developer-photo {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--bg-primary);
      box-shadow: 0 4px 12px rgba(99, 1, 22, 0.15);
      transition: all 0.3s ease;
    }

    .developer-card:hover .developer-photo {
      transform: scale(1.05);
      border-color: var(--accent-secondary);
    }

    /* Photo Badge */
    .photo-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      width: 36px;
      height: 36px;
      background: var(--accent-primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 3px solid white;
      box-shadow: 0 2px 8px rgba(99, 1, 22, 0.2);
    }

    .photo-badge svg {
      width: 18px;
      height: 18px;
      stroke: white;
    }

    /* Developer Info */
    .developer-name {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 4px;
      letter-spacing: -0.3px;
    }

    .developer-role {
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      font-weight: 500;
      margin-bottom: 20px;
    }

    .developer-description {
      font-size: 15px;
      line-height: 1.6;
      color: var(--text-dark);
      margin-bottom: 24px;
      min-height: 96px;
    }

    /* Skills Tags */
    .skills-wrapper {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: center;
      margin-bottom: 24px;
    }

    .skill-tag {
      display: inline-block;
      padding: 6px 12px;
      background: var(--bg-primary);
      color: var(--accent-success);
      font-size: 12px;
      font-weight: 500;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      border: 1px solid var(--bg-secondary);
    }

    /* Contact Button */
    .contact-email {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: var(--bg-primary);
      color: var(--accent-secondary);
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      border: 1px solid var(--bg-secondary);
      transition: all 0.2s ease;
    }

    .contact-email:hover {
      background: var(--accent-secondary);
      color: white;
      border-color: var(--accent-secondary);
      transform: translateY(-1px);
    }

    .contact-email svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
    }

    /* Team Section */
    .team-footer {
      text-align: center;
      padding: 40px 32px;
      background: white;
      border: 1px solid var(--bg-secondary);
      border-radius: 8px;
      animation: fadeIn 0.6s ease-out 0.4s backwards;
    }

    .team-footer h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 24px;
      font-weight: 600;
      color: var(--accent-primary);
      margin-bottom: 12px;
    }

    .team-footer p {
      font-size: 15px;
      color: var(--text-muted);
      line-height: 1.6;
      max-width: 600px;
      margin: 0 auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 72px;
        padding: 32px 24px;
      }

      .developer-grid {
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

      .developer-card {
        padding: 32px 24px;
      }

      .page-header h1 {
        font-size: 28px;
      }

      .developer-description {
        min-height: auto;
      }
    }
  </style>
</head>
<body>

   <?php include '../includes/Lsidebar.php'; ?>

  <div class="content-wrapper">
    <main class="main-content">

      <div class="page-header">
        <h1>Meet the Team</h1>
        <p>The talented minds behind the POS System</p>
      </div>

      <!-- Developer Grid -->
      <div class="developer-grid">

        <!-- Developer 2: Nirav -->
        <div class="developer-card">
          <div class="developer-photo-wrapper">
            <img src="static/nirav.jpg" alt="Nirav Thakur" class="developer-photo">
            <div class="photo-badge">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
              </svg>
            </div>
          </div>
          
          <h2 class="developer-name">Nirav Thakur</h2>
          <div class="developer-role">UI/UX Designer</div>
          
          <p class="developer-description">
            Creative force behind the elegant interface. Crafted intuitive user experiences and visual design that makes the POS system a pleasure to use every day.
          </p>
          
          <div class="skills-wrapper">
            <span class="skill-tag">UI Design</span>
            <span class="skill-tag">UX Design</span>
            <span class="skill-tag">Frontend</span>
          </div>
          
          <a href="mailto:nirav@possystem.com" class="contact-email">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            <span>Get in Touch</span>
          </a>
        </div>

        <!-- Developer 3: Rushab -->
        <div class="developer-card">
          <div class="developer-photo-wrapper">
            <img src="static/rushab.jpg" alt="Rushab Nhaikade" class="developer-photo">
            <div class="photo-badge">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
              </svg>
            </div>
          </div>
          
          <h2 class="developer-name">Rushab Nhaikade</h2>
          <div class="developer-role">Backend & DevOps</div>
          
          <p class="developer-description">
            Powers the system behind the scenes. Built robust backend infrastructure and seamless deployment pipelines ensuring reliable, scalable performance.
          </p>
          
          <div class="skills-wrapper">
            <span class="skill-tag">Backend</span>
            <span class="skill-tag">DevOps</span>
            <span class="skill-tag">Database</span>
          </div>
          
          <a href="mailto:rushab@possystem.com" class="contact-email">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            <span>Get in Touch</span>
          </a>
        </div>

      </div>

      <!-- Team Footer -->
      <div class="team-footer">
        <h3>Built with Passion</h3>
        <p>
          Our diverse team combines technical expertise with creative vision to deliver a POS system 
          that's both powerful and delightful to use. We're committed to continuous improvement 
          and exceptional user experience.
        </p>
      </div>

    </main>
  </div>

</body>
</html>
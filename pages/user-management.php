<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include __DIR__ . '/../config/dbcon.php';

$current_page = basename($_SERVER['PHP_SELF']);
$flash_error  = '';
$flash_success = '';

/* ── Handle Add / Edit ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $password = $_POST['password'] ?? '';

    if ($username && $role) {
        if ($id > 0) {                          // ── Edit existing
            if ($password) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username=?, role=?, password_hash=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $role, $password_hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
                $stmt->bind_param("ssi", $username, $role, $id);
            }
            $stmt->execute();
            $flash_success = "User <strong>" . htmlspecialchars($username) . "</strong> updated successfully.";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=updated&name=" . urlencode($username));
            exit();
        } else {                                // ── Create new
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE username=?");
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $flash_error = "Username <strong>" . htmlspecialchars($username) . "</strong> already exists.";
            } else {
                if ($password === '') {
                    $flash_error = "Password is required for new users.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("sss", $username, $password_hash, $role);
                    $stmt->execute();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=created&name=" . urlencode($username));
                    exit();
                }
            }
        }
    }
}

/* ── Handle Delete ── */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=deleted");
        exit();
    }
}

/* ── Flash messages from redirect ── */
if (isset($_GET['success'])) {
    $name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
    switch ($_GET['success']) {
        case 'created': $flash_success = "User <strong>$name</strong> created successfully."; break;
        case 'updated': $flash_success = "User <strong>$name</strong> updated successfully."; break;
        case 'deleted': $flash_success = "User deleted successfully.";                         break;
    }
}

/* ── Fetch all users ── */
$users = [];
$res   = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) { $users[] = $row; }
}

/* ── Check if editing ── */
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt    = $conn->prepare("SELECT id, username, role FROM users WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link rel="stylesheet" href="static/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    /* ─── CSS Variables ─── */
    :root {
      --bg-primary:       #FBF9F5;
      --bg-secondary:     #CEC3C1;
      --accent-primary:   #630116;
      --accent-secondary: #AF5B73;
      --accent-success:   #7E892B;
      --text-dark:        #2a2a2a;
      --text-muted:       #6a6a6a;
      --shadow-rest:      0 2px 8px rgba(99,1,22,0.04);
      --shadow-hover:     0 8px 24px rgba(99,1,22,0.12);
      --radius-sm:        6px;
      --radius-md:        8px;
    }

    /* ─── Reset ─── */
    *, *::before, *::after {
      margin: 0; padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      color: var(--text-dark);
      min-height: 100vh;
    }

    /* ─── Layout ─── */
    .content-wrapper {
      margin-left: 260px;
      min-height: 100vh;
      padding: 48px 40px;
      transition: margin-left 0.3s ease;
    }
    .main-content {
      max-width: 1100px;
      margin: 0 auto;
    }

    /* ─── Page Header ─── */
    .page-header {
      margin-bottom: 36px;
      animation: fadeIn 0.5s ease-out;
    }
    .page-header h1 {
      font-family: 'Crimson Pro', serif;
      font-size: 38px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.5px;
      margin-bottom: 6px;
    }
    .page-header p {
      font-size: 15px;
      color: var(--text-muted);
    }

    /* ─── Alerts ─── */
    .alert {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 18px;
      border-radius: var(--radius-sm);
      font-size: 14px;
      margin-bottom: 28px;
    }
    .alert svg {
      width: 20px; height: 20px;
      flex-shrink: 0; fill: none;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .alert--error {
      background: #fff5f5;
      border: 1px solid var(--accent-primary);
      color: var(--accent-primary);
      animation: shake 0.3s ease-out;
    }
    .alert--error svg { stroke: var(--accent-primary); }
    .alert--success {
      background: #f0f4e8;
      border: 1px solid var(--accent-success);
      color: var(--accent-success);
      animation: slideIn 0.3s ease-out;
    }
    .alert--success svg { stroke: var(--accent-success); }

    /* ─── Two-Column Layout: Form | Table ─── */
    .page-grid {
      display: grid;
      grid-template-columns: 380px 1fr;
      gap: 32px;
      align-items: start;
    }

    /* ─── Form Card ─── */
    .form-card {
      background: #fff;
      border: 1px solid var(--bg-secondary);
      border-radius: var(--radius-md);
      padding: 36px 32px;
      box-shadow: var(--shadow-rest);
      position: sticky;
      top: 48px;
      animation: fadeIn 0.5s ease-out 0.1s backwards;
      transition: box-shadow 0.3s ease;
    }
    .form-card:hover { box-shadow: var(--shadow-hover); }

    /* Card header row */
    .form-card__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--bg-secondary);
    }
    .form-card__header h3 {
      font-family: 'Crimson Pro', serif;
      font-size: 22px;
      font-weight: 600;
      color: var(--accent-primary);
      letter-spacing: -0.3px;
    }
    /* "Edit" pill shown when editing */
    .badge--edit {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: #fff;
      background: var(--accent-secondary);
      padding: 4px 10px;
      border-radius: 12px;
    }

    /* ─── Form Elements ─── */
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-dark);
      margin-bottom: 8px;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 14px 16px;
      font-size: 15px;
      font-family: 'Work Sans', sans-serif;
      background: var(--bg-primary);
      border: 1px solid var(--bg-secondary);
      border-radius: var(--radius-sm);
      color: var(--text-dark);
      transition: all 0.2s ease;
      appearance: none;
      -webkit-appearance: none;
    }
    .form-group input::placeholder { color: #999; }
    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--accent-secondary);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
      outline: none;
    }

    /* Custom select arrow */
    .select-wrap {
      position: relative;
    }
    .select-wrap::after {
      content: '';
      position: absolute;
      right: 16px; top: 50%;
      transform: translateY(-50%);
      width: 0; height: 0;
      border-left: 5px solid transparent;
      border-right: 5px solid transparent;
      border-top: 5px solid var(--text-muted);
      pointer-events: none;
    }
    .select-wrap select { padding-right: 36px; }

    /* Password hint */
    .form-hint {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    /* ─── Buttons ─── */
    .btn-primary {
      width: 100%;
      padding: 15px;
      font-size: 14px;
      font-weight: 600;
      font-family: 'Work Sans', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      background: var(--accent-primary);
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 8px;
    }
    .btn-primary:hover {
      background: #4a010f;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(99,1,22,0.25);
    }
    .btn-primary:active { transform: translateY(0); box-shadow: none; }

    .btn-cancel {
      display: block;
      width: 100%;
      margin-top: 12px;
      padding: 10px;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Work Sans', sans-serif;
      color: var(--text-muted);
      background: none;
      border: 1px solid var(--bg-secondary);
      border-radius: var(--radius-sm);
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      transition: all 0.2s ease;
    }
    .btn-cancel:hover {
      background: var(--bg-secondary);
      color: var(--text-dark);
    }

    /* ─── Table Wrapper ─── */
    .table-section {
      animation: fadeIn 0.5s ease-out 0.15s backwards;
    }
    .table-section__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }
    .section-label {
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--text-muted);
    }
    .user-count {
      font-size: 13px;
      color: var(--text-muted);
    }
    .user-count strong {
      color: var(--accent-primary);
      font-weight: 600;
    }

    .table-wrapper {
      background: #fff;
      border: 1px solid var(--bg-secondary);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-rest);
      overflow: hidden;
    }

    table { width: 100%; border-collapse: collapse; }

    thead { background: var(--accent-primary); }
    thead th {
      font-family: 'Work Sans', sans-serif;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: #fff;
      padding: 15px 20px;
      text-align: left;
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 1px solid rgba(206,195,193,0.5);
      transition: background 0.2s ease;
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(251,249,245,0.85); }

    tbody td {
      font-size: 14px;
      color: var(--text-dark);
      padding: 14px 20px;
      vertical-align: middle;
    }
    tbody td.col-id {
      color: var(--text-muted);
      font-weight: 500;
      font-size: 13px;
    }
    tbody td.col-username { font-weight: 500; }

    /* ─── Role Badges ─── */
    .role-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 14px;
    }
    .role-badge svg {
      width: 14px; height: 14px;
      fill: none; stroke: currentColor;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }
    .role-badge--admin {
      background: rgba(99,1,22,0.08);
      color: var(--accent-primary);
    }
    .role-badge--staff {
      background: rgba(175,91,115,0.1);
      color: var(--accent-secondary);
    }

    /* ─── Action Buttons ─── */
    .actions {
      display: flex;
      gap: 8px;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Work Sans', sans-serif;
      border-radius: var(--radius-sm);
      border: 1px solid var(--bg-secondary);
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
      background: var(--bg-primary);
    }
    .btn-action svg {
      width: 14px; height: 14px;
      fill: none; stroke: currentColor;
      stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }

    .btn-action--edit   { color: var(--accent-secondary); }
    .btn-action--edit:hover {
      background: var(--accent-secondary);
      color: #fff;
      border-color: var(--accent-secondary);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(175,91,115,0.25);
    }

    .btn-action--delete { color: var(--accent-primary); }
    .btn-action--delete:hover {
      background: var(--accent-primary);
      color: #fff;
      border-color: var(--accent-primary);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(99,1,22,0.25);
    }

    /* ─── Empty State ─── */
    .empty-state {
      text-align: center;
      padding: 56px 24px;
      color: var(--text-muted);
    }
    .empty-state svg {
      width: 52px; height: 52px;
      margin: 0 auto 16px;
      opacity: 0.3;
      stroke: var(--bg-secondary);
      fill: none;
      stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;
    }
    .empty-state p { font-size: 15px; }

    /* ─── Animations ─── */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shake {
      0%,100% { transform: translateX(0); }
      25%     { transform: translateX(-8px); }
      75%     { transform: translateX(8px); }
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-10px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 900px) {
      .page-grid {
        grid-template-columns: 1fr;
      }
      .form-card { position: static; }
    }
    @media (max-width: 768px) {
      .content-wrapper { margin-left: 72px; padding: 32px 24px; }
      .page-header h1  { font-size: 32px; }
    }
    @media (max-width: 480px) {
      .content-wrapper { margin-left: 0; padding: 24px 16px; }
      .page-header h1  { font-size: 28px; }
      .form-card       { padding: 28px 22px; }
      .table-wrapper   { overflow-x: auto; }
      table            { min-width: 560px; }
    }
  </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
<main class="main-content">

  <!-- ── Page Header ── -->
  <div class="page-header">
    <h1>User Management</h1>
    <p>Add new staff or admin, edit existing users, or remove users</p>
  </div>

  <!-- ── Alerts ── -->
  <?php if (!empty($flash_error)): ?>
    <div class="alert alert--error">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= $flash_error ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($flash_success)): ?>
    <div class="alert alert--success">
      <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <?= $flash_success ?>
    </div>
  <?php endif; ?>

  <!-- ── Two-Column Grid ── -->
  <div class="page-grid">

    <!-- ── Left: Add / Edit Form ── -->
    <div class="form-card">
      <div class="form-card__header">
        <h3><?= $edit_user ? 'Edit User' : 'Add New User' ?></h3>
        <?php if ($edit_user): ?>
          <span class="badge--edit">Editing</span>
        <?php endif; ?>
      </div>

      <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $edit_user['id'] ?? 0 ?>">

        <!-- Username -->
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 placeholder="e.g. john_doe"
                 required
                 value="<?= htmlspecialchars($edit_user['username'] ?? '') ?>"
                 autocomplete="off">
        </div>

        <!-- Role -->
        <div class="form-group">
          <label for="role">Role</label>
          <div class="select-wrap">
            <select id="role" name="role" required>
              <option value="">Select a role</option>
              <option value="Admin"  <?= ($edit_user['role'] ?? '') === 'Admin'  ? 'selected' : '' ?>>Admin</option>
              <option value="Staff"  <?= ($edit_user['role'] ?? '') === 'Staff'  ? 'selected' : '' ?>>Staff</option>
            </select>
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="<?= $edit_user ? 'New password (optional)' : 'Set password' ?>">
          <?php if ($edit_user): ?>
            <p class="form-hint">Leave blank to keep the current password.</p>
          <?php endif; ?>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-primary">
          <?= $edit_user ? 'Update User' : 'Add User' ?>
        </button>
      </form>

      <!-- Cancel link only shown while editing -->
      <?php if ($edit_user): ?>
        <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn-cancel">Cancel</a>
      <?php endif; ?>
    </div>

    <!-- ── Right: Users Table ── -->
    <section class="table-section">
      <div class="table-section__header">
        <span class="section-label">All Users</span>
        <span class="user-count"><strong><?= count($users) ?></strong> user<?= count($users) !== 1 ? 's' : '' ?></span>
      </div>

      <div class="table-wrapper">
        <?php if (!empty($users)): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Role</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
              <td class="col-id"><?= $user['id'] ?></td>
              <td class="col-username"><?= htmlspecialchars($user['username']) ?></td>
              <td>
                <?php if ($user['role'] === 'Admin'): ?>
                  <span class="role-badge role-badge--admin">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Admin
                  </span>
                <?php else: ?>
                  <span class="role-badge role-badge--staff">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Staff
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions">
                  <a href="?edit=<?= $user['id'] ?>" class="btn-action btn-action--edit">
                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                  </a>
                  <a href="?delete=<?= $user['id'] ?>" class="btn-action btn-action--delete"
                     onclick="return confirm('Delete this user? This cannot be undone.');">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Delete
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php else: ?>
          <div class="empty-state">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <p>No users have been added yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

  </div><!-- /page-grid -->
</main>
</div>

</body>
</html>
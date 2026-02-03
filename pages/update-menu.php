<?php
session_start();
include __DIR__ . '/../config/dbcon.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

// Fetch all menu items
$menuData = [];
$result = $conn->query("SELECT id, name, price FROM items WHERE is_active=1 ORDER BY name ASC");
while($row = $result->fetch_assoc()){
    $menuData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Menu Items</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Google Fonts -->
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

/* ─── layout (match dashboard) ─── */
.content-wrapper {
  margin-left: 260px;
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.main-content {
  padding: 48px 40px;
  animation: fadeIn 0.5s ease-out;
}

/* ─── page header ─── */
.page-header {
  margin-bottom: 48px;
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
  font-weight: 400;
  font-family: 'Work Sans', sans-serif;
}

/* ─── alert banners ─── */
#alert-container { margin-bottom: 24px; }

.alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Work Sans', sans-serif;
}
.alert i { font-size: 16px; flex-shrink: 0; }

.alert-error {
  background: #fff5f5;
  border: 1px solid var(--accent-primary);
  color: var(--accent-primary);
  animation: shake 0.3s ease-out;
}
.alert-success {
  background: #f0f4e8;
  border: 1px solid var(--accent-success);
  color: var(--accent-success);
  animation: slideIn 0.3s ease-out;
}

/* ─── card ─── */
.menu-card {
  background: #fff;
  border: 1px solid var(--bg-secondary);
  border-radius: 8px;
  padding: 36px 32px;
  box-shadow: 0 2px 8px rgba(99,1,22,0.04);
  transition: box-shadow 0.3s ease;
  margin-bottom: 32px;
}
.menu-card:hover {
  box-shadow: 0 8px 24px rgba(99,1,22,0.12);
}

/* ─── section heading ─── */
.section-title {
  font-family: 'Crimson Pro', serif;
  font-size: 24px;
  font-weight: 600;
  color: var(--accent-primary);
  letter-spacing: -0.3px;
  margin-bottom: 24px;
}

/* ─── table ─── */
.menu-table-wrap {
  overflow-x: auto;
  border: 1px solid var(--bg-secondary);
  border-radius: 8px;
}
#menu-management-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Work Sans', sans-serif;
}
#menu-management-table thead {
  background: var(--bg-primary);
}
#menu-management-table th {
  text-align: left;
  padding: 13px 16px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted);
  border-bottom: 1px solid var(--bg-secondary);
  white-space: nowrap;
}
#menu-management-table tbody tr {
  border-bottom: 1px solid var(--bg-secondary);
  transition: background 0.2s ease;
}
#menu-management-table tbody tr:last-child { border-bottom: none; }
#menu-management-table tbody tr:hover { background: #fdfcfa; }
#menu-management-table td {
  padding: 10px 16px;
  vertical-align: middle;
}

/* row enter */
#menu-management-table tbody tr.row-in {
  animation: rowIn 0.3s ease-out backwards;
}

/* ─── inputs ─── */
.menu-input {
  width: 100%;
  padding: 10px 14px;
  font-size: 15px;
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  border: 1px solid var(--bg-secondary);
  border-radius: 6px;
  color: var(--text-dark);
  transition: all 0.2s ease;
}
.menu-input::placeholder { color: #999; }
.menu-input:focus {
  border-color: var(--accent-secondary);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(175,91,115,0.1);
  outline: none;
}
.menu-input-price { max-width: 120px; }

/* ─── buttons ─── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 26px;
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
  white-space: nowrap;
}
.btn-primary:hover {
  background: #4a010f;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99,1,22,0.25);
}
.btn-primary:active { transform: translateY(0); box-shadow: none; }

.btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  font-family: 'Work Sans', sans-serif;
  background: var(--bg-primary);
  color: var(--accent-secondary);
  border: 1px solid var(--bg-secondary);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.btn-secondary:hover {
  background: var(--accent-secondary);
  color: #fff;
  border-color: var(--accent-secondary);
  transform: translateY(-1px);
}
.btn-secondary:active { transform: translateY(0); }

.btn-danger {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  font-family: 'Work Sans', sans-serif;
  background: #fff5f5;
  color: var(--accent-primary);
  border: 1px solid rgba(99,1,22,0.2);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.btn-danger:hover {
  background: var(--accent-primary);
  color: #fff;
  border-color: var(--accent-primary);
  transform: translateY(-1px);
}
.btn-danger:active { transform: translateY(0); }

/* ─── add-item layout ─── */
.add-item-row {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  flex-wrap: wrap;
}
.add-item-row .field {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
  min-width: 160px;
}
.add-item-row .field-price { flex: 0 0 140px; }
.add-item-row label {
  font-size: 12px;
  font-weight: 500;
  color: var(--text-dark);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-family: 'Work Sans', sans-serif;
}

/* ─── empty state ─── */
.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: var(--text-muted);
  font-family: 'Work Sans', sans-serif;
  font-size: 15px;
}
.empty-state i {
  font-size: 36px;
  color: var(--bg-secondary);
  margin-bottom: 12px;
  display: block;
}

/* ─── keyframes ─── */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25%      { transform: translateX(-8px); }
  75%      { transform: translateX(8px); }
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes rowIn {
  from { opacity: 0; transform: translateX(-12px); }
  to   { opacity: 1; transform: translateX(0); }
}

/* ─── responsive ─── */
@media (max-width: 768px) {
  .content-wrapper { margin-left: 72px; }
  .main-content    { padding: 32px 24px; }

  .page-header h1  { font-size: 30px; }
  .menu-card       { padding: 28px 20px; }
  .add-item-row    { flex-direction: column; align-items: stretch; }
  .add-item-row .field,
  .add-item-row .field-price { flex: 1; min-width: 0; }
}
@media (max-width: 480px) {
  .content-wrapper { margin-left: 0; }
  .main-content    { padding: 24px 16px; }

  .page-header h1  { font-size: 28px; }
  .menu-card       { padding: 20px 16px; }
  #menu-management-table th,
  #menu-management-table td { padding: 8px 10px; font-size: 13px; }
  .btn-primary, .btn-secondary, .btn-danger { padding: 8px 12px; font-size: 12px; }
}
</style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
  <main class="main-content">

    <header class="page-header">
      <h1>Manage Menu Items</h1>
      <p>Add, update or remove items from the menu</p>
    </header>

    <!-- alert banner (JS fills this) -->
    <div id="alert-container"></div>

    <!-- current items -->
    <div class="menu-card">
      <h3 class="section-title">Current Items</h3>

      <div class="menu-table-wrap">
        <table id="menu-management-table">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Price (₹)</th>
              <th style="width:110px;">Update</th>
              <th style="width:110px;">Remove</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!-- add new item -->
    <div class="menu-card">
      <h3 class="section-title">Add New Item</h3>

      <div class="add-item-row">
        <div class="field">
          <label for="new-item-name">Item Name</label>
          <input type="text" id="new-item-name" class="menu-input" placeholder="e.g. Margherita Pizza">
        </div>
        <div class="field field-price">
          <label for="new-item-price">Price (₹)</label>
          <input type="number" id="new-item-price" class="menu-input menu-input-price" placeholder="0.00" min="0" step="0.01">
        </div>
        <button id="add-item-btn" class="btn-primary">
          <i class="fa-solid fa-plus"></i> Add Item
        </button>
      </div>
    </div>

  </main>
</div>

<script>
(function(){

  let menuData = <?php echo json_encode($menuData); ?>;

  const tbody      = document.getElementById('menu-management-table').querySelector('tbody');
  const alertBox   = document.getElementById('alert-container');
  const nameInput  = document.getElementById('new-item-name');
  const priceInput = document.getElementById('new-item-price');

  /* ── alerts ── */
  let alertTimer = null;
  function showAlert(msg, type) {
    if(alertTimer) clearTimeout(alertTimer);
    const icon = type === 'success'
      ? '<i class="fa-solid fa-circle-check"></i>'
      : '<i class="fa-solid fa-circle-exclamation"></i>';
    alertBox.innerHTML =
      '<div class="alert alert-' + type + '" role="alert">' + icon + '<span>' + msg + '</span></div>';
    alertTimer = setTimeout(function(){ alertBox.innerHTML = ''; }, 4000);
  }

  /* ── escape ── */
  function esc(s){
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  /* ── render table ── */
  function renderMenuManagement() {
    tbody.innerHTML = '';

    if(menuData.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="4">' +
          '<div class="empty-state">' +
            '<i class="fa-solid fa-list"></i>' +
            '<p>No menu items yet — add one below.</p>' +
          '</div>' +
        '</td></tr>';
      return;
    }

    menuData.forEach(function(item, index){
      const tr = document.createElement('tr');
      tr.className = 'row-in';
      tr.style.animationDelay = (index * 0.04) + 's';

      tr.innerHTML =
        '<td><input type="text"   class="menu-input edit-name"  data-index="' + index + '" value="' + esc(item.name) + '"></td>' +
        '<td><input type="number" class="menu-input menu-input-price edit-price" data-index="' + index + '" value="' + item.price + '" min="0" step="0.01"></td>' +
        '<td><button class="btn-secondary edit-btn" data-index="' + index + '" data-id="' + item.id + '"><i class="fa-solid fa-pen-to-square"></i> Update</button></td>' +
        '<td><button class="btn-danger remove-btn"  data-index="' + index + '" data-id="' + item.id + '"><i class="fa-solid fa-trash-can"></i> Remove</button></td>';

      tbody.appendChild(tr);
    });
  }

  /* ── add item ── */
  document.getElementById('add-item-btn').addEventListener('click', function(){
    const name  = nameInput.value.trim();
    const price = parseFloat(priceInput.value);

    if(!name || isNaN(price) || price < 0) {
      showAlert('Please enter a valid item name and price.', 'error');
      return;
    }

    fetch('../handlers/save_item.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: name, price: price })
    })
    .then(function(res){ return res.json(); })
    .then(function(data){
      if(data.success){
        menuData.push({ id: data.id, name: name, price: price });
        renderMenuManagement();
        nameInput.value  = '';
        priceInput.value = '';
        showAlert('Item "' + esc(name) + '" added successfully.', 'success');
      } else {
        showAlert('Error saving item: ' + (data.error || 'unknown'), 'error');
      }
    })
    .catch(function(){ showAlert('Network error. Please try again.', 'error'); });
  });

  /* ── edit / remove (delegated) ── */
  tbody.addEventListener('click', function(e){
    const btn = e.target.closest('button');
    if(!btn) return;

    const index = parseInt(btn.dataset.index, 10);
    const id    = btn.dataset.id;

    if(btn.classList.contains('edit-btn')){
      const newName  = tbody.querySelector('.edit-name[data-index="'  + index + '"]').value.trim();
      const newPrice = parseFloat(tbody.querySelector('.edit-price[data-index="' + index + '"]').value);

      if(!newName || isNaN(newPrice) || newPrice < 0) {
        showAlert('Please enter a valid name and price before updating.', 'error');
        return;
      }

      fetch('../handlers/update_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, name: newName, price: newPrice })
      })
      .then(function(res){ return res.json(); })
      .then(function(data){
        if(data.success){
          menuData[index].name  = newName;
          menuData[index].price = newPrice;
          showAlert('Item updated successfully.', 'success');
        } else {
          showAlert('Failed to update: ' + (data.error || 'unknown'), 'error');
        }
      })
      .catch(function(){ showAlert('Network error. Please try again.', 'error'); });
    }

    if(btn.classList.contains('remove-btn')){
      if(!confirm('Remove "' + menuData[index].name + '"?')) return;

      fetch('../handlers/delete_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      })
      .then(function(res){ return res.json(); })
      .then(function(data){
        if(data.success){
          const removed = menuData.splice(index, 1)[0];
          renderMenuManagement();
          showAlert('Item "' + esc(removed.name) + '" removed.', 'success');
        } else {
          showAlert('Failed to remove: ' + (data.error || 'unknown'), 'error');
        }
      })
      .catch(function(){ showAlert('Network error. Please try again.', 'error'); });
    }
  });

  /* ── init ── */
  renderMenuManagement();

})();
</script>

</body>
</html>
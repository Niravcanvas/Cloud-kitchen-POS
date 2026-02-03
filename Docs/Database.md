# Database Configuration — `cake_cafe_db`

All database info for the CakeCafe POS system. Use this to set up or restore the database from scratch.

---

## Connection Settings

Defined in `config/dbcon.php`:

| Parameter | Value         |
|-----------|---------------|
| Host      | localhost     |
| User      | root          |
| Password  | *(empty)*     |
| Database  | cake_cafe_db  |

```php
<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "cake_cafe_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
```

---

## Tables Overview

| Table        | Purpose                                          | Key Relationships                        |
|--------------|--------------------------------------------------|------------------------------------------|
| users        | Staff and admin accounts                         | Referenced by `orders.user_id`           |
| customers    | Customer records (including Guest)               | Referenced by `orders.customer_id`       |
| items        | Menu items with price, cost, category            | Referenced by `order_items` and `feedback` |
| orders       | Each transaction — links customer, user, payment | Parent of `order_items`                  |
| order_items  | Line items inside each order                     | Belongs to `orders`, references `items`  |
| feedback     | Per-item ratings and comments from customers     | References `customers` and `items`       |
| invoices     | Tracks generated PDF invoice files               | References `orders`                      |

---

## Table Schemas

### `users`

Stores staff and admin accounts. Passwords are stored as bcrypt hashes.

| Column        | Type         | Notes                              |
|---------------|--------------|------------------------------------|
| id            | int(11) PK   | Auto-increment                     |
| username      | varchar(100) | Unique                             |
| password_hash | varchar(255) | bcrypt via `password_hash()`       |
| role          | enum          | `Admin` or `Staff`, default `Staff` |
| created_at    | datetime     | Auto-set to current timestamp      |

**To add a new user via phpMyAdmin**, set `password_hash` to the output of:
```php
password_hash('yourpassword', PASSWORD_BCRYPT);
```

---

### `customers`

Stores customer details. Row ID `16` is reserved as the default `Guest` entry — orders without a named customer use this.

| Column | Type         | Notes                |
|--------|--------------|----------------------|
| id     | int(11) PK   | Auto-increment       |
| name   | varchar(100) | Required             |
| email  | varchar(100) | Nullable             |
| mobile | varchar(15)  | Nullable             |

---

### `items`

The menu. Each item has a selling price, cost, and calculated margin. `is_active` controls whether it shows up in the POS.

| Column     | Type          | Notes                                        |
|------------|---------------|----------------------------------------------|
| id         | int(11) PK    | Auto-increment                               |
| name       | varchar(100)  | Required                                     |
| price      | decimal(10,2) | Selling price shown in POS                   |
| category   | varchar(50)   | `Cakes`, `Pastries`, `Coffee`, `Beverages`, `Snacks`, `Misc` |
| is_active  | tinyint(1)    | `1` = visible in POS, `0` = hidden           |
| cost       | decimal(10,2) | Cost to make (used in margin reports)        |
| margin     | decimal(10,2) | `price - cost`                               |
| created_at | datetime      | Auto-set                                     |

**Current categories and item count:**

| Category   | Items |
|------------|-------|
| Cakes      | 8     |
| Pastries   | 4     |
| Coffee     | 4     |
| Beverages  | 3     |
| Snacks     | 2     |
| Misc       | 1     |

---

### `orders`

One row per transaction. Tracks the customer, which staff member processed it, payment details, and current status.

| Column        | Type          | Notes                                                          |
|---------------|---------------|----------------------------------------------------------------|
| id            | int(11) PK    | Auto-increment                                                 |
| customer_id   | int(11) FK    | References `customers.id`                                      |
| order_time    | datetime      | Auto-set to current timestamp                                  |
| status        | enum          | `Pending`, `Preparing`, `Completed`, `Cancelled`               |
| total         | decimal(10,2) | Sum of all line items                                          |
| user_id       | int(11) FK    | References `users.id` — the staff who made the order           |
| payment_mode  | varchar(50)   | `Cash`, `Card`, or `UPI`                                       |
| amount_taken  | decimal(10,2) | Amount the customer actually paid                              |
| change_amount | decimal(10,2) | `amount_taken - total`                                         |

---

### `order_items`

Each row is one line item inside an order. An order can have multiple rows here.

| Column   | Type          | Notes                                  |
|----------|---------------|----------------------------------------|
| id       | int(11) PK    | Auto-increment                         |
| order_id | int(11) FK    | References `orders.id` (CASCADE delete)|
| item_id  | int(11) FK    | References `items.id`                  |
| quantity | int(11)       | How many of this item                  |
| price    | decimal(10,2) | Unit price at time of order            |
| total    | decimal(10,2) | `price × quantity`                     |

> `price` is stored at order time so historical reports stay accurate even if the menu price changes later.

---

### `feedback`

Customer ratings and comments, linked to a specific menu item.

| Column      | Type          | Notes                                    |
|-------------|---------------|------------------------------------------|
| id          | int(11) PK    | Auto-increment                           |
| customer_id | int(11) FK    | References `customers.id`                |
| item_id     | int(11) FK    | References `items.id`                    |
| rating      | int(11)       | `1` to `5` (CHECK constraint enforced)   |
| comment     | text          | Nullable                                 |
| created_at  | datetime      | Auto-set                                 |

---

### `invoices`

Tracks which orders have had PDF invoices generated and where the file is saved.

| Column    | Type         | Notes                                          |
|-----------|--------------|------------------------------------------------|
| id        | int(11) PK   | Auto-increment                                 |
| order_id  | int(11) FK   | References `orders.id`                         |
| file_path | varchar(255) | Relative path e.g. `invoices/invoice_1.pdf`    |
| issued_at | datetime     | Auto-set                                       |

---

## Foreign Key Relationships

```
users          ←── orders.user_id
customers      ←── orders.customer_id
orders         ←── order_items.order_id   (CASCADE on delete)
orders         ←── invoices.order_id
items          ←── order_items.item_id
items          ←── feedback.item_id
customers      ←── feedback.customer_id
```

---

## Setup Steps

1. Open phpMyAdmin → `http://localhost/phpmyadmin`
2. Create a new database named exactly `cake_cafe_db` with charset `utf8mb4` and collation `utf8mb4_unicode_ci`
3. Import the SQL dump file (the full dump is kept in the project docs)
4. Verify all 7 tables are present and populated
5. Done — the app will connect automatically using `config/dbcon.php`

---

## Quick Reference: Common Queries

**Get all active menu items (used by POS):**
```sql
SELECT id, name, price FROM items WHERE is_active = 1 ORDER BY name ASC;
```

**Get full order with line items:**
```sql
SELECT o.id, o.order_time, c.name AS customer,
       i.name AS item, oi.quantity, oi.total
FROM orders o
JOIN customers c    ON o.customer_id = c.id
JOIN order_items oi ON oi.order_id   = o.id
JOIN items i        ON oi.item_id    = i.id
WHERE o.id = 1;
```

**Average rating per item:**
```sql
SELECT i.name, ROUND(AVG(f.rating), 1) AS avg_rating, COUNT(f.id) AS total_reviews
FROM items i
LEFT JOIN feedback f ON f.item_id = i.id
GROUP BY i.id
ORDER BY avg_rating DESC;
```

**Daily revenue summary:**
```sql
SELECT DATE(order_time) AS order_date,
       COUNT(*) AS total_orders,
       SUM(total) AS revenue
FROM orders
WHERE status = 'Completed'
GROUP BY DATE(order_time)
ORDER BY order_date DESC;
```
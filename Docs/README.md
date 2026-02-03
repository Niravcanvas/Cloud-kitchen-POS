# CakeCafe POS System

A clean, professional Point-of-Sale system built for CakeCafe — handles orders, invoices, kitchen display, sales reports, demand forecasting, menu management, and customer feedback.

---

## Tech Stack

- **Frontend** — PHP, HTML, CSS (Work Sans + Crimson Pro), vanilla JavaScript
- **Backend** — PHP 8.2
- **Database** — MariaDB 10.4 (`cake_cafe_db`)
- **Server** — XAMPP
- **PDF Generation** — FPDF

---

## Project Structure

```
CakeCafe/
│
├── index.php                  # Login page (entry point)
├── home.php                   # Dashboard (after login)
│
├── assets/
│   ├── css/
│   │   └── style.css          # Global stylesheet
│   ├── images/
│   │   ├── image.jpg
│   │   └── N1.jpg
│   └── fpdf/                  # FPDF library
│       └── fpdf.php
│
├── config/
│   └── dbcon.php              # Database connection
│
├── includes/
│   ├── sidebar.php            # Main app sidebar (after login)
│   ├── Lsidebar.php           # Login page sidebar
│   └── auth.php               # Login handler + session check
│
├── handlers/
│   ├── save_order.php         # Process new orders
│   ├── save_item.php          # Add menu items
│   ├── update_item.php        # Update menu items
│   ├── delete_item.php        # Remove menu items
│   ├── invoice.php            # Generate PDF invoices
│   └── logout.php             # Destroy session & redirect
│
├── pages/
│   ├── pos.php                # Point of Sale
│   ├── kitchen.php            # Kitchen order display
│   ├── history.php            # Order history + filters
│   ├── sales-report.php       # Sales analytics
│   ├── forecast.php           # Demand forecasting
│   ├── menu-optimization.php  # Menu performance analysis
│   ├── update-menu.php        # Add / edit / remove menu items
│   ├── customer-feedback.php  # View feedback
│   ├── feedback-tablet.php    # Tablet-facing feedback form
│   ├── user-management.php    # User CRUD
│   ├── settings.php           # App settings
│   ├── about.php              # About page
│   ├── contact.php            # Contact page
│   └── developers.php         # Team page
│
├── invoices/                  # Auto-generated PDF invoices
│
├── Docs/
│   ├── README.md              # This file
│   ├── database.md            # Database schema & setup
│   └── Masterprompt.md        # Frontend design system
│
├── .gitignore
└── Masterprompt.md            # Design reference (root copy)
```

---

## Setup & Installation

### 1. Prerequisites

- XAMPP installed and running (Apache + MySQL/MariaDB)
- PHP 8.2+

### 2. Clone or Copy the Project

Place the `CakeCafe` folder inside your XAMPP `htdocs` directory:

```
/Applications/XAMPP/xamppfiles/htdocs/CakeCafe/
```

### 3. Database Setup

- Open phpMyAdmin → `http://localhost/phpmyadmin`
- Create a new database named `cake_cafe_db`
- Import the SQL dump (see `Docs/database.md` for the full schema)

### 4. Run the App

Open your browser and go to:

```
http://localhost/CakeCafe/
```

You will land on the login page.

### 5. Default Credentials

| Username | Role  |
|----------|-------|
| nirav    | Admin |
| admin    | Admin |
| rahul    | Staff |
| pushkar  | Staff |

> Passwords are hashed. Use the ones you set during initial database setup, or reset them via phpMyAdmin using `password_hash('yourpassword', PASSWORD_BCRYPT)` in the `users` table.

---

## File Path Rules

Every folder has its own relative path pattern. Follow these strictly or links and includes will break.

**Root level** (`index.php`, `home.php`):
```php
include 'config/dbcon.php';
include 'includes/sidebar.php';
// CSS: assets/css/style.css
```

**Pages folder** (`pages/*.php`):
```php
include __DIR__ . '/../config/dbcon.php';
include '../includes/sidebar.php';
// CSS: ../assets/css/style.css
// Handlers: ../handlers/*.php
// Session redirect: ../index.php
```

**Handlers folder** (`handlers/*.php`):
```php
include __DIR__ . '/../config/dbcon.php';
// Redirects: ../pages/*.php  or  ../index.php
// FPDF: __DIR__ . '/../assets/fpdf/fpdf.php'
```

**Includes folder** (`includes/*.php`):
```php
include __DIR__ . '/../config/dbcon.php';
// Redirects: ../index.php
```

---

## Key Features

- **POS** — Build orders from active menu items, apply payment, generate invoices
- **Kitchen Display** — Live view of pending and in-progress orders
- **Order History** — Filterable by date range and searchable by order ID, item, or customer
- **Sales Report** — Revenue and order analytics
- **Demand Forecast** — Predict future item demand based on past orders
- **Menu Optimization** — Analyze which items perform best by margin and sales
- **Menu Management** — Add, update, or deactivate menu items on the fly
- **Customer Feedback** — Collect and view per-item ratings and comments
- **User Management** — Admin/Staff CRUD with role-based access
- **Invoice Generation** — Auto-generates PDF invoices via FPDF

---

## Contributing

- Keep all new page files inside `pages/`
- All backend logic goes in `handlers/`
- Reusable UI components go in `includes/`
- Follow the path rules above — no hardcoded absolute paths
- Use CSS variables defined in `:root` for all colors
- Reference `Masterprompt.md` for design guidelines before building any new UI
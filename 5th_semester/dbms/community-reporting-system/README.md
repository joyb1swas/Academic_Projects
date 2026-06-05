# Community Problem Reporting System

A PHP/MySQL web application that allows citizens to report and track local community issues (potholes, broken streetlights, garbage, etc.) and lets administrators manage those reports.

---

## How to Run (Step by Step)

### Prerequisites

- **XAMPP**, **WAMP**, or **Laragon** (Apache + PHP 7.4+ + MySQL)
- A web browser (Chrome, Firefox, Edge, etc.)

---

### Step 1: Place the Project in the Server Root

Copy the entire `community-reporting-system` folder into your web server's document root:

XAMPP - `C:\xampp\htdocs\`

### Step 2: Start Apache and MySQL

Open your server control panel and **Start** both services:

- **Apache** (or HTTP server)
- **MySQL** (or MariaDB)

---

### Step 3: Open the Application in Your Browser

Navigate to:

```
http://localhost/community-reporting-system/
```

> **The database is created automatically on first visit.**  
> `config/database.php` reads `sql/schema.sql` and creates the `community_reports` database, all tables, and the default admin account if the database doesn't already exist.

---

### Step 4: (Optional) Configure Database Credentials

If your MySQL setup uses a different username or requires a password, edit `config/database.php`:

```php
$host     = 'localhost';
$dbname   = 'community_reports';
$username = 'root';
$password = '';
```

---

### Default Admin Account

Log in at `http://localhost/community-reporting-system/login.php` with:

| Field    | Value     |
| -------- | --------- |
| Username | `admin`   |
| Password | `admin12` |

---

## Project Structure

```
community-reporting-system/
├── admin/
│   └── dashboard.php        # Admin panel
├── config/
│   └── database.php         # DB connection & auto-setup
├── css/
│   └── style.css            # Styles
├── includes/
│   ├── footer.php           # Shared footer
│   └── header.php           # Shared header
├── js/
│   └── script.js            # Frontend scripts
├── sql/
│   └── schema.sql           # Database schema & seed data
├── uploads/                 # Report image uploads
├── index.php                # Public homepage with report listing
├── dashboard.php            # User dashboard
├── login.php                # Login page
├── logout.php               # Logout
├── register.php             # Registration page
├── report.php               # Submit a new report
├── report-detail.php        # View a single report with comments
└── README.md
```

## Features

- **User authentication** – Register, login, logout
- **Role-based access** – Regular users and admins
- **Report management** – Submit reports with title, description, location, category, and optional image
- **Status tracking** – Reports move through `pending` → `in_progress` → `resolved`
- **Comments** – Users and admins can comment on reports
- **Category filtering & pagination** – Browse and filter reports
- **Admin dashboard** – View all reports, change statuses, manage the system
 
## Images 

- In the ui/ux folder you will get the interface of the system

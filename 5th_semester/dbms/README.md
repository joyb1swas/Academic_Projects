# Community Problem Reporting System

## 1. Overview

The **Community Problem Reporting System** is a web-based application that allows residents to report local issues (e.g., potholes, broken streetlights, garbage buildup) and enables administrators to track, manage, and resolve those reports efficiently. It serves as a bridge between the community and local authorities.

---

## 2. Technology Stack

| Component  | Technology                       |
| ---------- | -------------------------------- |
| Frontend   | HTML5, CSS3, JavaScript          |
| Backend    | PHP 7.4+ (vanilla, no framework) |
| Database   | MySQL                            |
| Web Server | Apache (via XAMPP/WAMP/Laragon)  |
| Styling    | Custom CSS (responsive)          |

---

## 3. Database Schema

### Database Name: `community_reports`

### 3.1 Table: `users`

Stores registered user accounts.

| Column     | Type                 | Constraints                 | Description            |
| ---------- | -------------------- | --------------------------- | ---------------------- |
| id         | INT                  | PRIMARY KEY, AUTO_INCREMENT | Unique user ID         |
| username   | VARCHAR(50)          | NOT NULL, UNIQUE            | User's username        |
| email      | VARCHAR(100)         | NOT NULL, UNIQUE            | User's email address   |
| password   | VARCHAR(255)         | NOT NULL                    | Bcrypt hashed password |
| role       | ENUM('user','admin') | DEFAULT 'user'              | User role              |
| created_at | TIMESTAMP            | DEFAULT CURRENT_TIMESTAMP   | Account creation date  |

### 3.2 Table: `categories`

Defines problem categories for reports.

| Column | Type         | Constraints                 | Description        |
| ------ | ------------ | --------------------------- | ------------------ |
| id     | INT          | PRIMARY KEY, AUTO_INCREMENT | Unique category ID |
| name   | VARCHAR(100) | NOT NULL, UNIQUE            | Category name      |

**Default categories:** Roads, Streetlights, Garbage, Water, Electricity, Noise, Other

### 3.3 Table: `reports`

Stores problem reports submitted by users.

| Column      | Type                                     | Constraints                            | Description                  |
| ----------- | ---------------------------------------- | -------------------------------------- | ---------------------------- |
| id          | INT                                      | PRIMARY KEY, AUTO_INCREMENT            | Unique report ID             |
| user_id     | INT                                      | NOT NULL, FOREIGN KEY → users(id)      | Reporter's user ID           |
| title       | VARCHAR(255)                             | NOT NULL                               | Brief problem title          |
| description | TEXT                                     | NOT NULL                               | Detailed problem description |
| location    | VARCHAR(255)                             | NOT NULL                               | Problem location/address     |
| category_id | INT                                      | NOT NULL, FOREIGN KEY → categories(id) | Problem category             |
| status      | ENUM('pending','in_progress','resolved') | DEFAULT 'pending'                      | Current status of the report |
| image       | VARCHAR(255)                             | DEFAULT NULL                           | Path to uploaded image       |
| created_at  | TIMESTAMP                                | DEFAULT CURRENT_TIMESTAMP              | Submission time              |
| updated_at  | TIMESTAMP                                | DEFAULT CURRENT_TIMESTAMP ON UPDATE    | Last update time             |

### 3.4 Table: `comments`

Stores comments/discussion on reports.

| Column     | Type      | Constraints                         | Description             |
| ---------- | --------- | ----------------------------------- | ----------------------- |
| id         | INT       | PRIMARY KEY, AUTO_INCREMENT         | Unique comment ID       |
| report_id  | INT       | NOT NULL, FOREIGN KEY → reports(id) | Associated report       |
| user_id    | INT       | NOT NULL, FOREIGN KEY → users(id)   | Commenter's user ID     |
| comment    | TEXT      | NOT NULL                            | Comment text            |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP           | Comment submission time |

### Entity Relationship

```
users 1──N reports 1──N comments
                │
                N
categories 1──N reports
```

---

## 4. System Architecture

### 4.1 Frontend (Presentation Layer)

- **HTML/CSS/JS** renders all UI components
- Responsive design with CSS Grid and Flexbox
- Mobile-friendly layout adapts to screens under 768px
- JavaScript provides auto-dismissing alerts and file input feedback

### 4.2 Backend (Application Layer)

- **Vanilla PHP** handles all server-side logic
- **PDO** (PHP Data Objects) with prepared statements for secure database interaction
- Session-based authentication using `$_SESSION`
- Passwords hashed with `password_hash()` (bcrypt)
- File upload handling with type and size validation (max 5MB, JPG/PNG/GIF/WebP)

### 4.3 Database Layer

- **MySQL** relational database
- Foreign key constraints with `ON DELETE CASCADE`
- Timestamp-based tracking for report updates

---

## 5. Features — Detailed Breakdown

### 5.1 User Authentication

- **Register:** Create account with username, email, password
- **Login:** Authenticate via username or email; auto-redirect admins to admin panel
- **Logout:** Destroy session and redirect to home
- **Role-based access:** Users see only their content; admins see all content

### 5.2 Report Management

- **Submit Report:** Form with title, category dropdown, location, description (textarea), and optional image upload
- **List Reports:** Grid view on home page with pagination (9 per page)
- **Filter Reports:** By status (pending/in-progress/resolved) and category
- **View Details:** Single report page showing full information

### 5.3 Image Upload

- Supported formats: JPEG, PNG, GIF, WebP
- File size limit: 5 MB
- Stored in `uploads/` directory with unique filenames
- Displayed as thumbnail in grid and full-size in detail view

### 5.4 Comment System

- Authenticated users can post comments on any report
- Comments displayed chronologically with username and timestamp
- Comments are tied to both user and report (cascading delete)

### 5.5 Admin Dashboard

- **Statistics cards:** Total reports, pending, in-progress, resolved counts
- **Filter controls:** Status and category filters
- **Data table:** All reports with inline status update dropdown
- **Actions:** View report details, delete report
- **Admin-only routes:** Protected by session role check

### 5.6 Pagination

- Home page displays 9 reports per page
- Pagination links at the bottom of the report grid
- Filter state is preserved across page navigation

---

## 6. Security Measures

| Measure                | Implementation                                     |
| ---------------------- | -------------------------------------------------- |
| SQL Injection          | PDO prepared statements with parameterized queries |
| Password Hashing       | `password_hash()` with bcrypt algorithm            |
| XSS Protection         | `htmlspecialchars()` on all user output            |
| Session Management     | PHP sessions with `session_start()`                |
| File Upload Validation | Check file type (MIME) and size before saving      |
| Access Control         | Role check (`$_SESSION['role']`) on admin routes   |
| Input Validation       | Server-side validation on all form submissions     |

---

## 7. User Roles & Permissions

| Feature              | Guest | Regular User | Admin |
| -------------------- | ----- | ------------ | ----- |
| View public reports  | ✅    | ✅           | ✅    |
| Register / Login     | ✅    | —            | —     |
| Submit a report      | ❌    | ✅           | ✅    |
| View own reports     | ❌    | ✅           | ✅    |
| Comment on reports   | ❌    | ✅           | ✅    |
| View all reports     | ❌    | ❌           | ✅    |
| Update report status | ❌    | ❌           | ✅    |
| Delete any report    | ❌    | ❌           | ✅    |
| View admin dashboard | ❌    | ❌           | ✅    |

---

## 8. Page Directory

| Page                  | URL                      | Description                        |
| --------------------- | ------------------------ | ---------------------------------- |
| Home / Public Listing | `index.php`              | Paginated public report grid       |
| User Registration     | `register.php`           | New user sign-up form              |
| User Login            | `login.php`              | Authentication form                |
| Logout                | `logout.php`             | Session termination                |
| Submit Report         | `report.php`             | Report submission form (auth)      |
| My Reports            | `dashboard.php`          | User's own reports (auth)          |
| Report Detail         | `report-detail.php?id=N` | Single report + comments           |
| Admin Dashboard       | `admin/dashboard.php`    | Full management panel (admin only) |

---

## 9. Design

- **Color scheme:** Blue primary (`#1a73e8`), neutral grays, status-colored badges (yellow/blue/green)
- **Layout:** Fixed-width container (max 1200px), centered content
- **Typography:** System font stack (`-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, ...`)
- **Card-based UI:** Reports displayed as cards with hover elevation effect
- **Status badges:** Pill-shaped labels — yellow (pending), blue (in-progress), green (resolved)

---

## 10. Future Enhancements (Possible Extensions)

- Email notifications when report status changes
- Map integration (Google Maps / Leaflet) for location pinning
- Upvote system to prioritize reports
- Report assignment to specific admin/officers
- REST API for mobile app integration
- Export reports to CSV/PDF
- Two-factor authentication
- Password reset via email
- Dark mode toggle

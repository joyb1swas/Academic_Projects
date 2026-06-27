# Smart Business Analytics - System Information

### 1. Project Overview

# Smart Business Analytics

## Software Development Project (SDP) – 5th Semester

Smart Business Analytics is a lightweight web-based application developed as the final project for the **Software Development Project (SDP)** course in the **5th Semester**. It helps small businesses and startups manage products, record sales, track expenses, and monitor business performance through a real-time analytics dashboard.

## Features

- Product management
- Sales recording
- Expense tracking
- Real-time profit and revenue analytics
- Interactive charts and dashboards
- Top and low-performing product analysis
- AI-based business suggestions

## Use Cases

This system is suitable for small businesses, retail shops, startups, and educational demonstrations of business analytics and management systems.

## Course Information

**Course:** Software Development Project (SDP)
**Semester:** 5th Semester
**Project Type:** Final Course Project

### 2. Technology Stack & Architecture

**Architecture:** Client-Server architecture utilizing RESTful APIs.

- **Frontend UI:** HTML5, CSS3.
- **Frontend Logic:** Vanilla JavaScript.
- **Data Visualization:** Chart.js (Interactive Bar & Doughnut charts).
- **Backend Framework:** Python 3 with Flask Framework.
- **Database:** MySQL (Hosted via XAMPP).
- **Database Library:** `mysql-connector-python`.

### 3. Core Functional Requirements (Features)

- **Product Management:** Users can add, view, and delete sellable products (includes ID, Name, and Price). Deleting a product automatically cascades and deletes all its historical sales to preserve math integrity.
- **Sales Recording:** Users can record sales by selecting a product, entering the quantity, and selecting a date. The backend automatically calculates the total transaction price.
- **Expense Tracking:** Users can log operational costs by category, amount, and date.
- **Dashboard & Analytics:**
  - Calculates live Net Profit (Total Revenue - Total Expenses).
  - Calculates Month-over-Month (MoM) revenue growth percentage (e.g., `↑ 15% vs previous`).
  - Calculates the Expense Ratio (what percentage of revenue is consumed by expenses).
- **Sales Leaderboard:** Analyzes historical data to rank the "Top 3 Performers" and "Bottom 3 Slow Movers".
- **Smart Suggestions Engine:** A backend algorithm calculates the average revenue across all products. Any product generating less than 50% of the overall average is flagged, and the dashboard generates an actionable business suggestion (like running a discount) for that specific item.

### 4. Non-Functional & System Behaviors

- **Network Portability:** The backend binds to `0.0.0.0` and utilizes Cross-Origin Resource Sharing (CORS). This allows any device (phones, tablets, other laptops) connected to the same local Wi-Fi network to access the web app without installing anything.
- **Auto-Database Initialization:** Upon starting, the Python backend automatically connects to MySQL and executes the `schema.sql` file to construct the database and tables if they do not exist, completely removing the need for manual setup in phpMyAdmin.

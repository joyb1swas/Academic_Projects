# AI Showcase & Blog (PHP Project)

A PHP-based web application that displays AI products from a MySQL database and includes a searchable AI blog. The project automatically creates the database, inserts sample data, and provides a clean UI using HTML and CSS.

## Features

### 1. AI Product Showcase

* Automatically creates the database `ai_showcase` and the table `ai_products` (if not already created).
* Inserts five AI-related products into the database on first run.
* Displays product cards with name, year invented, description, and image.

### 2. Blog Search System

* User must type **"AI Blog"** to access the blog.
* Uses PHP `header()` redirect for navigation.
* Shows validation message if input is incorrect.

### 3. AI Blog Page

* Well-structured AI blog containing:

  * Introduction to AI
  * Main goals of AI
  * AI-generated images
  * Types of AI
  * Core AI areas (table)
  * Real-world applications
  * Advantages and disadvantages
  * Conclusion
* Includes a button linking to the AI Product Showcase.

## Technologies Used

* PHP
* MySQL
* HTML5
* CSS3
* XAMPP / WAMP for local server

## Folder Structure

```
project-folder/
│── ai_products.php
│── blog.html
│── index.php
│── style.css
│── style1.css
│── style2.css
│── img/
│   ├── alexa.jpg
│   ├── sophia.jpg
│   ├── assistant.jpg
│   ├── chatgpt.jpg
│   ├── tesla.jpg
│   ├── ai1.jpg
│   ├── ai2.jpg
```

## How to Run

1. Install XAMPP/WAMP/LAMP.
2. Start Apache and MySQL.
3. Place the project folder in:

   ```
   htdocs/ (for XAMPP)
   ```
4. Open in browser:

   ```
   http://localhost/your-project-folder/index.php
   ```
5. The database will be created automatically.

## Usage

1. Enter **AI Blog** on the search page.
2. Access the AI blog page.
3. Click "View AI Products" to open the product showcase.
4. View AI product cards loaded from MySQL.


## Author

Joy Biswas
https://github.com/joyb1swas

## License

This project is available for educational and personal use.
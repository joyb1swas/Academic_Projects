from flask import Flask, render_template, request, jsonify
from flask_cors import CORS
import mysql.connector
from config import Config
from decimal import Decimal
import os

app = Flask(__name__)
CORS(app)
app.config.from_object(Config)

def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host=app.config["DB_HOST"],
            user=app.config["DB_USER"],
            password=app.config["DB_PASSWORD"],
            database=app.config["DB_NAME"]
        )
        return conn, conn.cursor(dictionary=True)
    except mysql.connector.Error as err:
        print(f"Error connecting to MySQL: {err}")
        return None, None

@app.route("/")
def dashboard_page():
    return render_template("dashboard.html")

@app.route("/products")
def products_page():
    return render_template("products.html")

@app.route("/sales")
def sales_page():
    return render_template("sales.html")

@app.route("/expenses")
def expenses_page():
    return render_template("expenses.html")

# --- REST APIs ---

# Products API
@app.route("/api/products", methods=["GET", "POST"])
def api_products():
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    if request.method == "GET":
        cursor.execute("SELECT * FROM products ORDER BY id DESC")
        products = cursor.fetchall()
        for p in products: p["price"] = float(p["price"])
        conn.close()
        return jsonify(products)
        
    elif request.method == "POST":
        data = request.json
        name = data.get("name")
        price = data.get("price")
        
        if not name or not price:
            return jsonify({"error": "Name and price are required"}), 400
            
        cursor.execute("INSERT INTO products (name, price) VALUES (%s, %s)", (name, price))
        conn.commit()
        conn.close()
        return jsonify({"message": "Product added successfully"}), 201

@app.route("/api/products/<int:prod_id>", methods=["DELETE"])
def api_delete_product(prod_id):
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    cursor.execute("DELETE FROM products WHERE id = %s", (prod_id,))
    conn.commit()
    conn.close()
    return jsonify({"message": "Product deleted successfully"}), 200

# Sales API
@app.route("/api/sales", methods=["GET", "POST"])
def api_sales():
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    if request.method == "GET":
        query = """
            SELECT s.id, s.product_id, p.name as product_name, s.quantity, s.total_price, s.date 
            FROM sales s 
            JOIN products p ON s.product_id = p.id 
            ORDER BY s.date DESC, s.id DESC
        """
        cursor.execute(query)
        sales = cursor.fetchall()
        for s in sales: s["total_price"] = float(s["total_price"])
        conn.close()
        return jsonify(sales)
        
    elif request.method == "POST":
        data = request.json
        product_id = data.get("product_id")
        quantity = data.get("quantity")
        date = data.get("date")
        
        if not product_id or not quantity or not date:
            return jsonify({"error": "Missing fields"}), 400
            
        cursor.execute("SELECT price FROM products WHERE id = %s", (product_id,))
        product = cursor.fetchone()
        
        if not product:
            return jsonify({"error": "Product not found"}), 404
            
        total_price = float(product["price"]) * int(quantity)
        
        cursor.execute(
            "INSERT INTO sales (product_id, quantity, total_price, date) VALUES (%s, %s, %s, %s)",
            (product_id, quantity, total_price, date)
        )
        conn.commit()
        conn.close()
        return jsonify({"message": "Sale recorded successfully"}), 201

# Expenses API
@app.route("/api/expenses", methods=["GET", "POST"])
def api_expenses():
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    if request.method == "GET":
        cursor.execute("SELECT * FROM expenses ORDER BY date DESC, id DESC")
        expenses = cursor.fetchall()
        for e in expenses: e["amount"] = float(e["amount"])
        conn.close()
        return jsonify(expenses)
        
    elif request.method == "POST":
        data = request.json
        category = data.get("category")
        amount = data.get("amount")
        date = data.get("date")
        
        if not category or not amount or not date:
            return jsonify({"error": "Missing fields"}), 400
            
        cursor.execute("INSERT INTO expenses (category, amount, date) VALUES (%s, %s, %s)", (category, amount, date))
        conn.commit()
        conn.close()
        return jsonify({"message": "Expense recorded successfully"}), 201

# Dashboard API
@app.route("/api/dashboard/summary", methods=["GET"])
def api_dashboard_summary():
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    cursor.execute("SELECT SUM(total_price) as rev FROM sales")
    rev_row = cursor.fetchone()
    total_revenue = float(rev_row["rev"]) if rev_row and rev_row["rev"] else 0.0
    
    cursor.execute("SELECT SUM(amount) as exp FROM expenses")
    exp_row = cursor.fetchone()
    total_expenses = float(exp_row["exp"]) if exp_row and exp_row["exp"] else 0.0
    
    net_profit = total_revenue - total_expenses
    
    # Chart data - monthly sales
    cursor.execute("""
        SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(total_price) as monthly_sales 
        FROM sales 
        GROUP BY month 
        ORDER BY month
    """)
    monthly_sales = cursor.fetchall()
    for row in monthly_sales: row["monthly_sales"] = float(row["monthly_sales"])
    
    # Chart data - expenses by category
    cursor.execute("""
        SELECT category, SUM(amount) as category_amount 
        FROM expenses 
        GROUP BY category
    """)
    expenses_by_category = cursor.fetchall()
    for row in expenses_by_category: row["category_amount"] = float(row["category_amount"])
    
    conn.close()
    
    # Calculate simple growth logic & ratios
    revenue_growth_str = ""
    if len(monthly_sales) >= 2:
        last_month = monthly_sales[-1]["monthly_sales"]
        prev_month = monthly_sales[-2]["monthly_sales"]
        if prev_month > 0:
            growth = ((last_month - prev_month) / prev_month) * 100
            revenue_growth_str = f"↑ {growth:.1f}% vs previous" if growth >= 0 else f"↓ {abs(growth):.1f}% vs previous"
            
    expense_ratio = 0
    if total_revenue > 0:
        expense_ratio = (total_expenses / total_revenue) * 100

    return jsonify({
        "total_revenue": total_revenue,
        "total_expenses": total_expenses,
        "net_profit": net_profit,
        "monthly_sales": monthly_sales,
        "expenses_by_category": expenses_by_category,
        "revenue_growth": revenue_growth_str,
        "expense_ratio": f"{expense_ratio:.1f}% of Revenue" if expense_ratio > 0 else ""
    })

@app.route("/api/dashboard/leaderboard", methods=["GET"])
def api_leaderboard():
    conn, cursor = get_db_connection()
    if not conn: return jsonify({"top": [], "bottom": []})
    
    query = """
        SELECT p.name, COALESCE(SUM(s.total_price), 0) as revenue
        FROM products p
        LEFT JOIN sales s ON p.id = s.product_id
        GROUP BY p.id, p.name
        ORDER BY revenue DESC
    """
    cursor.execute(query)
    data = cursor.fetchall()
    conn.close()
    
    if not data: return jsonify({"top": [], "bottom": []})
    
    for row in data: row["revenue"] = float(row["revenue"])
    
    top = data[:3]
    bottom = data[-3:]
    bottom.reverse()
    
    return jsonify({"top": top, "bottom": bottom})

@app.route("/api/dashboard/suggestions", methods=["GET"])
def api_suggestions():
    conn, cursor = get_db_connection()
    if not conn:
        return jsonify({"error": "Database connection failed"}), 500
        
    query = """
        SELECT p.id, p.name, COALESCE(SUM(s.total_price), 0) as total_revenue
        FROM products p
        LEFT JOIN sales s ON p.id = s.product_id
        GROUP BY p.id, p.name
    """
    cursor.execute(query)
    product_sales = cursor.fetchall()
    conn.close()
    
    if not product_sales:
        return jsonify([])
        
    revenues = [float(p["total_revenue"]) for p in product_sales]
    avg_revenue = sum(revenues) / len(revenues) if len(revenues) > 0 else 0
    threshold = avg_revenue * 0.5
    
    suggestions = []
    actions = [
        "Consider offering a discount for this product.",
        "Promote the product through advertising or social media.",
        "Bundle this product with popular items.",
        "Review the product price.",
        "Improve product visibility in the store."
    ]
    
    import random
    for p in product_sales:
        rev = float(p["total_revenue"])
        if rev < threshold:
            suggestions.append({
                "product_name": p["name"],
                "revenue": rev,
                "suggestion": random.choice(actions)
            })
            
    return jsonify(suggestions)

def init_db():
    try:
        # Connect to MySQL server globally to create the DB if missing
        server_conn = mysql.connector.connect(
            host=app.config["DB_HOST"],
            user=app.config["DB_USER"],
            password=app.config["DB_PASSWORD"]
        )
        cursor = server_conn.cursor()
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {app.config['DB_NAME']}")
        server_conn.commit()
        
        cursor.execute(f"USE {app.config['DB_NAME']}")
        
        # Auto-run the schema.sql script to build tables
        schema_path = os.path.join(os.path.dirname(__file__), 'database', 'schema.sql')
        if os.path.exists(schema_path):
            with open(schema_path, 'r') as f:
                sql_script = f.read()
            for statement in sql_script.split(';'):
                if statement.strip():
                    cursor.execute(statement)
            server_conn.commit()
            
        cursor.close()
        server_conn.close()
        print("MySQL Database auto-initialized successfully! Ready to go.")
    except Exception as e:
        print(f"Warning: Could not auto-initialize MySQL DB. Is XAMPP MySQL running? Error: {e}")

if __name__ == "__main__":
    init_db()
    app.run(host='0.0.0.0', debug=True, port=5000)

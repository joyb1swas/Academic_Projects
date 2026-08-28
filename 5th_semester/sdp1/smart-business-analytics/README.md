# Smart Business Analytics

A simple web-based system for small businesses to track products, sales, expenses, and calculate business results. It features a dashboard with revenue calculations, visual charts, and a smart suggestion system.

## Setup Instructions

### 1. Database Setup (MySQL via XAMPP)

1. Open your **XAMPP Control Panel** and click **Start** next to both **Apache** and **MySQL**.

### 2. Backend Setup

1. Open your terminal or command prompt and navigate to the project folder (`project_sdp`).
2. Create a virtual environment and activate the venv:
   ```bash
   * python -m venv venv
   *  venv\Scripts\activate
   ```
3. Install the required Python packages:
   ```bash
   pip install -r requirements.txt
   ```

### 3. Run the Application

1. Start the Flask dev server:
   ```bash
   python app.py
   ```
2. **On the host PC:** Open your web browser and go to: `http://localhost:5000`
3. **On any other device (like a phone or another laptop):** Make sure the device is connected to the same Wi-Fi network, then type your host computer's local IP address into the browser followed by `:5000` (for example: `http://192.168.1.15:5000`).

import os

class Config:
    # Flask settings
    SECRET_KEY = os.environ.get("SECRET_KEY", "dev_secret_key_123")
    
    # MySQL Database settings for XAMPP (default root, no password)
    DB_HOST = "localhost"
    DB_USER = "root"
    DB_PASSWORD = ""
    DB_NAME = "smart_business_analytics"

<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_showcase";


$conn = new mysqli($servername, $username, $password);
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->close();


$conn = new mysqli($servername, $username, $password, $dbname);


$conn->query("
CREATE TABLE IF NOT EXISTS ai_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    year_invented VARCHAR(10),
    description TEXT,
    image_url VARCHAR(500)
)");


$result = $conn->query("SELECT COUNT(*) AS total FROM ai_products");
$row = $result->fetch_assoc();


if ($row['total'] == 0) {
    $conn->query("
        INSERT INTO ai_products (name, year_invented, description, image_url) VALUES
        ('Amazon Alexa', '2014', 'Alexa is a virtual assistant created by Amazon. It can answer questions, control devices, play music, and help with tasks.', 'img/alexa.jpg'),
        ('Sophia Robot', '2016', 'Sophia is a social humanoid robot by Hanson Robotics. She uses AI for facial expressions, speech, and interaction.', 'img/sophia.jpg'),
        ('Google Assistant', '2016', 'Google Assistant helps with voice commands, answering questions, reminders, and smart home control.', 'img/assistant.jpg'),
        ('ChatGPT', '2022', 'ChatGPT is an AI language model that can chat, write, code, and answer questions using natural language processing.', 'img/chatgpt.jpg'),
        ('Tesla Autopilot', '2015', 'Tesla Autopilot uses AI and sensors to assist driving, lane keeping, braking, and semi-autonomous navigation.', 'img/tesla.jpg')
    ");
}


$products = $conn->query("SELECT * FROM ai_products ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>AI Product Showcase</title>
    <link rel="stylesheet" href="style1.css">

</head>

<body>
    <header class="header">
        <h1>AI Product Showcase</h1>
    </header>

    <div class="product-container">
        <?php while ($p = $products->fetch_assoc()) { ?>
            <div class="product">
                <img src="<?php echo $p['image_url']; ?>" alt="<?php echo $p['name']; ?>">
                <h2><?php echo $p['name']; ?></h2>
                <div class="year">Invented: <?php echo $p['year_invented']; ?></div>
                <p><?php echo $p['description']; ?></p>
            </div>
        <?php } ?>
    </div>

    <a href="blog.html" class="back-btn">Back to Blog</a>


</body>

</html>
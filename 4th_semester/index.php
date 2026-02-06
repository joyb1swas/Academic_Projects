<?php
if (isset($_POST['submit'])) {
    $name = $_POST['text'];

    if ($name === "AI Blog") {
        header("Location: blog.html");
        exit();
    } else {
        echo "<p style='color:red;'>Wrong input. Please type exactly: AI Blog</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
</head>
<link rel="stylesheet" href="style2.css">

<body>
    <form method="post">
        <fieldset>
            <br>
            <caption>
                <h2>Search Your Blog Here</h2>
            </caption>
            <br>
            <input type="text" name="text">

            <p> Write "AI Blog" In The Box To See The Article </p>

            <input type="submit" name="submit" value="Enter">
            <br><br>
        </fieldset>

    </form>


</body>

</html>
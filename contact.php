<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <h1>Contact Us</h1>
            <p class="subtitle">Get in touch!</p>
        </div>
    </header>

    <main class="page-content">
        <div class="contact-form">
            <h2>Send us a message</h2>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                include 'db_connect.php';

                $name = htmlspecialchars($_POST["name"]);
                $email = htmlspecialchars($_POST["email"]);
                $message = htmlspecialchars($_POST["message"]);
                $created_at = date("Y-m-d H:i:s");

                if (empty($name) || empty($email) || empty($message)) {
                    echo "<p class='error'>Please fill in all fields.</p>";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<p class='error'>Invalid email format.</p>";
                } else {
                    $sql = "INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $name, $email, $message, $created_at);

                    if ($stmt->execute()) {
                        echo "<p class='success'>Your message has been sent!</p>";
                    } else {
                        echo "<p class='error'>Error sending message: " . $stmt->error . "</p>";
                    }

                    $stmt->close();
                    $conn->close();
                }
            }
            ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name"><br><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email"><br><br>
                <label for="message">Message:</label><br>
                <textarea id="message" name="message" rows="5"></textarea><br><br>
                <input type="submit" value="Send Message" class="submit-button">
            </form>
        </div>
    </main>

    <footer class="site-footer">
        <p>&copy; <?php echo date("Y"); ?> My Beautiful Website. All rights reserved.</p>
    </footer>
    <link rel="stylesheet" href="style.css">
    <style>
        .contact-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .contact-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .contact-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .contact-form input[type=text],
        .contact-form input[type=email],
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .contact-form textarea {
            resize: vertical;
        }
        .contact-form .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .contact-form .submit-button:hover {
            background-color: #45a049;
        }
        .contact-form .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .contact-form .success {
            color: green;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</body>
</html>
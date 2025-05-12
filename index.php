<?php
require_once('C:\xampp\htdocs\phptest\private\db_config.php');

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
	$email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    try {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        $stmt = $db->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        
        $successMessage = "<p class='message-success'>Message sent! Thank you.</p>";
    } catch (Exception $e) {
        $errorMessage = "<p class='message-error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Theme Website</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="green-bg">
        <div class="header-content">
            <h1>Posolk</h1>
            <p>All about me (probably)</p>
        </div>
    </header>

    <nav class="dark-bg">
        <div class="nav-container">
            <div class="nav-links">
                <a href="#" class="nav-link active" data-section="home">Home</a>
                <a href="#" class="nav-link" data-section="gallery">Gallery</a>
                <a href="#" class="nav-link" data-section="blog">Blog</a>
                <a href="#" class="nav-link" data-section="contact">Contact</a>
            </div>
            <div class="nav-manage">
                <a href="login.php" class="nav-link">Manage</a>
            </div>
        </div>
    </nav>
	
    <main class="white-bg">
        <div id="content-container">
            <section style="margin-left: 100px;" id="home-section" class="content-section active">
                <h2>About Me</h2>
                <p style="margin-bottom: 20px;">This is my personal profile page, I guess you should know a little about me</p>
				<p>I'm currently a student of UNSRAT, studying 💻Informatics Engineering</p>
				<p style="margin-bottom: 20px;">I'm 19 years old and my birthday is in ♎October 16th</p>
				<h2>What I like</h2>
				<p>I like 🧃food, 🎮videogames, and obviously 🛠️programming (although I kinda hate it ngl)</p>
                <div class="profile-container">
                    <div class="profile-info">
                        <h2>Quote of the day</h2>
                        <p>On ne sait jamais ce que demain nous réserve.</p>
                    </div>
                    <div class="profile-image">
                        <img style="margin-top: -200px;" src="images\me.jpg" alt="Profile Image">
                    </div>
                </div>
            </section>


            <section id="gallery-section" class="content-section">
                <h2>The Gallery</h2>
                <p>Explore my visual collection of random assortments</p>
                <div class="gallery-grid">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="gallery-item">
                            <img src="images\image<?= $i ?>.jpg" alt="Gallery Image <?= $i ?>">
                        </div>
                    <?php endfor; ?>
                </div>
                <div id="gallery-modal" class="modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="modal-image">
                </div>
            </section>
			
			
			<section id="blog-section" class="content-section">
                <h2>The Kitchen</h2>
                <p>Read my latest posts about food</p>
                <div class="blog-grid">
                    <?php
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'pemweb');
                    
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    
                    $sql = "SELECT id, title, image, wiki_url, created_at FROM blog_posts ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="blog-item">';
                            echo '<img src="blog_images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                            echo '<p>Posted on: ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
                            echo '<a href="' . htmlspecialchars($row['wiki_url']) . '" target="_blank">Read more</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No blog posts yet.</p>';
                    }
                    $conn->close();
                    ?>
                </div>
            </section>
			
			
            <section id="contact-section" class="content-section">
                <h2>Contact Me</h2>
                <p>Send me a message & I might read it</p>
                <form method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
				    <?php 
                      if (!empty($successMessage)) echo $successMessage;
                      if (!empty($errorMessage)) echo $errorMessage;
                    ?>
                </form>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="dark-bg">
        <p>&copy; <?php echo date('Y'); ?> Resen N. Doaly. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
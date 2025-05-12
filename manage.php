<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'pemweb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_blog'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_blog'])) {
        $title = $_POST['title'];
        $wiki_url = $_POST['wiki_url'];
        
        // Handle file upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "blog_images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                // Generate unique filename
                $image = uniqid() . '.' . $imageFileType;
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, image, wiki_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $image, $wiki_url);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_blog'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $wiki_url = $_POST['wiki_url'];
        
        if (!empty($_FILES['image']['name'])) {
            // Handle file upload
            $target_dir = "blog_images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                $image = uniqid() . '.' . $imageFileType;
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
                
                $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, image = ?, wiki_url = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $image, $wiki_url, $id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, wiki_url = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $wiki_url, $id);
        }
        
        $stmt->execute();
        $stmt->close();
    }
}

// Get messages with search and sort
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$messages_query = "SELECT * FROM contacts WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $messages_query .= " AND (name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= 'sss';
}

if (!empty($date_from) && !empty($date_to)) {
    $messages_query .= " AND created_at BETWEEN ? AND ?";
    $params = array_merge($params, [$date_from, $date_to]);
    $types .= 'ss';
}

switch ($sort) {
    case 'name_asc':
        $messages_query .= " ORDER BY name ASC";
        break;
    case 'name_desc':
        $messages_query .= " ORDER BY name DESC";
        break;
    case 'oldest':
        $messages_query .= " ORDER BY created_at ASC";
        break;
    default:
        $messages_query .= " ORDER BY created_at DESC";
        break;
}

$stmt = $conn->prepare($messages_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$messages_result = $stmt->get_result();

// Get blogs
$blogs_result = $conn->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="green-bg">
        <div class="header-content">
            <h1>Manage Content</h1>
            <p>Admin Dashboard</p>
        </div>
    </header>

    <nav class="dark-bg">
        <div class="nav-container">
            <div class="nav-links">
                <a href="#" class="nav-link active" data-section="messages">Messages</a>
                <a href="#" class="nav-link" data-section="blogs">Blogs</a>
            </div>
            <div class="nav-manage">
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="white-bg">
        <div id="content-container">
            <!-- Messages Section -->
            <section id="messages-section" class="content-section active">
                <h2>Messages</h2>
                
                <div class="search-container">
                    <form method="GET" action="manage.php" class="search-form">
                        <input type="hidden" name="section" value="messages">
                        <div class="form-group">
                            <input type="text" name="search" placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">Search</button>
                        </div>
                        <div class="form-group">
                            <label>Sort by:</label>
                            <select name="sort">
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
                                <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest first</option>
                                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date range:</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                            <span>to</span>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                        </div>
                    </form>
                </div>
                
                <div class="messages-list">
                    <?php if ($messages_result->num_rows > 0): ?>
                        <?php while ($message = $messages_result->fetch_assoc()): ?>
                            <div class="message-item">
                                <h3><?= htmlspecialchars($message['name']) ?></h3>
                                <p><strong>Email:</strong> <?= htmlspecialchars($message['email']) ?></p>
                                <p><strong>Date:</strong> <?= date('F j, Y H:i', strtotime($message['created_at'])) ?></p>
                                <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No messages found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Blogs Section -->
            <section id="blogs-section" class="content-section">
                <h2>Manage Blog Posts</h2>
                
                <div class="blog-form-container">
                    <h3>Add New Blog Post</h3>
                    <form method="POST" action="manage.php?section=blogs" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Image:</label>
                            <input type="file" id="image" name="image" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label for="wiki_url">Wiki URL:</label>
                            <input type="url" id="wiki_url" name="wiki_url" required>
                        </div>
                        <button type="submit" name="add_blog" class="submit-btn">Add Blog Post</button>
                    </form>
                </div>
                
                <div class="blog-list">
                    <h3>Existing Blog Posts</h3>
                    <?php if ($blogs_result->num_rows > 0): ?>
                        <?php while ($blog = $blogs_result->fetch_assoc()): ?>
                            <div class="blog-item-admin">
                                <form method="POST" action="manage.php?section=blogs" enctype="multipart/form-data" class="edit-form">
                                    <input type="hidden" name="id" value="<?= $blog['id'] ?>">
                                    <div class="form-group">
                                        <label>Title:</label>
                                        <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Current Image:</label>
                                        <img src="blog_images/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" style="max-width: 200px;">
                                    </div>
                                    <div class="form-group">
                                        <label>New Image (leave blank to keep current):</label>
                                        <input type="file" name="image" accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label>Wiki URL:</label>
                                        <input type="url" name="wiki_url" value="<?= htmlspecialchars($blog['wiki_url']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Created:</label>
                                        <p><?= date('F j, Y H:i', strtotime($blog['created_at'])) ?></p>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="update_blog" class="submit-btn">Update</button>
                                        <button type="submit" name="delete_blog" class="delete-btn" onclick="return confirm('Are you sure you want to delete this blog post?')">Delete</button>
                                    </div>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No blog posts yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer class="dark-bg">
        <p>&copy; <?= date('Y') ?> Admin Dashboard. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
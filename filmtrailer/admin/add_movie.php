<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include '../includes/header.php';
include '../includes/db.php';

// File upload configuration
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$maxFileSize = 2 * 1024 * 1024; // 2MB
$uploadDir = '../assets/images/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate CSRF token (add this if you have CSRF protection)
        // if (!validateCsrfToken($_POST['csrf_token'])) { ... }

        // Handle file upload
        if (!isset($_FILES['image'])) {
            throw new Exception('No file uploaded');
        }

        $file = $_FILES['image'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload error: ' . $file['error']);
        }

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and WEBP allowed.');
        }

        if ($file['size'] > $maxFileSize) {
            throw new Exception('File too large. Max 2MB allowed.');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('movie_') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . $imageName;

        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to save uploaded file');
        }

        // Sanitize inputs
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $genre = htmlspecialchars($_POST['genre']);
        $release_date = $_POST['release_date'];
        $trailer_url = htmlspecialchars($_POST['trailer_url']);
        $image_url = 'assets/images/' . $imageName;

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO movies (title, description, genre, release_date, trailer_url, image_url) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $genre, $release_date, $trailer_url, $image_url]);
        
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <h2>Add New Movie</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label>Genre</label>
            <input type="text" name="genre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Release Date</label>
            <input type="date" name="release_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Trailer Embed Code</label>
            <textarea name="trailer_url" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Movie Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
            <small class="form-text text-muted">Allowed formats: JPG, PNG, WEBP (Max 2MB)</small>
        </div>
        <button type="submit" class="btn btn-primary">Add Movie</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
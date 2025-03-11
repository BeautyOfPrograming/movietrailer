<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include '../includes/header.php';
include '../includes/db.php';

// Fetch movie data if ID is provided
if (isset($_GET['id'])) {
    $movieId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        die("Movie not found.");
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movieId = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $trailer_url = $_POST['trailer_url'];

    // Handle image upload if a new file is provided
    if (!empty($_FILES['poster']['name'])) {
        $uploadDir = "../assets/images/posters/";
        $year = date('Y');
        $targetDir = $uploadDir . $year . '/';

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('movie_') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetPath)) {
            // Delete old image if it exists
            if (!empty($movie['image_url']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $movie['image_url'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $movie['image_url']);
            }

            // Update image URL
            $image_url = "assets/images/posters/$year/" . $filename;
        } else {
            die("Failed to upload image.");
        }
    } else {
        // Keep the old image if no new file is uploaded
        $image_url = $movie['image_url'];
    }

    // Update movie in the database
    $stmt = $pdo->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, release_date = ?, trailer_url = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$title, $description, $genre, $release_date, $trailer_url, $image_url, $movieId]);

    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>Edit Movie</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $movie['id'] ?>">
        
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($movie['title']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($movie['description']) ?></textarea>
        </div>
        
        <div class="mb-3">
            <label>Genre</label>
            <input type="text" name="genre" class="form-control" value="<?= htmlspecialchars($movie['genre']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Release Date</label>
            <input type="date" name="release_date" class="form-control" value="<?= htmlspecialchars($movie['release_date']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Trailer Embed Code</label>
            <textarea name="trailer_url" class="form-control" required><?= htmlspecialchars($movie['trailer_url']) ?></textarea>
        </div>
        
        <div class="mb-3">
            <label>Movie Poster</label>
            <input type="file" name="poster" class="form-control" accept="image/*">
            <small class="form-text text-muted">Current: <?= basename($movie['image_url']) ?></small>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Movie</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
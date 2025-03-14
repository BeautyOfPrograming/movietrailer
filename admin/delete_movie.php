<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include '../includes/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$movieId = $_GET['id'];

// Fetch the movie to get the image path
$stmt = $pdo->prepare("SELECT image_url FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if ($movie) {
    // Delete the image file if it exists
    if (!empty($movie['image_url']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $movie['image_url'])) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $movie['image_url']);
    }

    // Delete the movie from the database
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);
}

// Redirect back to the admin dashboard
header("Location: index.php");
exit;
?>
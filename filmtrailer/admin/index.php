<?php
//session_start();
//if (!isset($_SESSION['admin_logged_in'])) {
    //header("Location: login.php");
    //exit;
//}

include '../includes/header.php';
include '../includes/db.php';
?>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <a href="add_movie.php" class="btn btn-success mb-3">Add New Movie</a>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Release Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
            while ($movie = $stmt->fetch()) {
            ?>
            <tr>
                <td><?= $movie['title'] ?></td>
                <td><?= $movie['genre'] ?></td>
                <td><?= $movie['release_date'] ?></td>
                <td>
                    <a href="edit_movie.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_movie.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
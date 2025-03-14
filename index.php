<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Latest Movies</h2>
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC");
        while ($movie = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Get the image path from the database
            $imagePath = $movie['thumbnail_url'];
            
            // Create the thumbnail path
            // Assuming your thumbnails are named like "thumb_filename.ext"
            $thumbnailPath = str_replace(basename($imagePath), 'thumb_' . basename($imagePath), $imagePath);


        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <!-- Use thumbnail version -->
                <img src="<?= $imagePath ?>" //$thumbnailPath
                     class="card-img-top" 
                     alt="<?= htmlspecialchars($movie['title']) ?>"
                     onerror="this.onerror=null;this.src='<?= $imagePath ?>';"
                     loading="lazy"
		     style=" height: 200px;"
                     >

                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                    <p class="card-text"><?= substr(htmlspecialchars($movie['description']), 0, 100) ?>...</p>
                    <a href="movie.php?id=<?= $movie['id'] ?>" class="btn btn-primary">Watch Trailer</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
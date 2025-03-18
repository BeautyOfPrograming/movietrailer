<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Path to the file storing the last run time
$lastRunFile = 'last_scrape_time.txt';

// Check if the scraper should run
$shouldRunScraper = false;
if (file_exists($lastRunFile)) {
    $lastRunTime = file_get_contents($lastRunFile);
    $currentTime = time();
    // Run the scraper if it hasn't been run in the last 6 hours
    if (($currentTime - $lastRunTime) > 60) {
        $shouldRunScraper = true;
    }
} else {
    $shouldRunScraper = true;
}

// Run the scraper if needed
if ($shouldRunScraper) {
    // Update the last run time
    file_put_contents($lastRunFile, time());

    // Run the scraper asynchronously
    exec('php scrape_trailers.php > /dev/null 2>&1 &');
}
?>

<!-- Add this in your header.php or directly in index.php -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-img-top {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    .pagination .page-link {
        color: #007bff;
    }
    .pagination .page-link:hover {
        color: #0056b3;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Latest Movies</h2>
    <div class="row">
        <?php
        // Number of movies per page
        $moviesPerPage = 6;

        // Get the current page number from the query string, default to 1
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Calculate the offset for the SQL query
        $offset = ($currentPage - 1) * $moviesPerPage;

        // Get the total number of movies
        $totalMovies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();

        // Calculate the total number of pages
        $totalPages = ceil($totalMovies / $moviesPerPage);

        // Fetch the movies for the current page
        $stmt = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC LIMIT $moviesPerPage OFFSET $offset");
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
                <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>"
                     onerror="this.onerror=null;this.src='<?= $imagePath ?>';" loading="lazy" style="height: 200px;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                    <p class="card-text"><?= substr(htmlspecialchars($movie['description']), 0, 100) ?>...</p>
                    <a href="movie.php?id=<?= $movie['id'] ?>" class="btn btn-primary">Watch Trailer</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Pagination controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $currentPage - 1 ?>">Previous</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>
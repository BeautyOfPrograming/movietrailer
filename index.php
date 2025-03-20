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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(135deg, #1a1c20 0%, #0c0e10 100%);
        color: white;
    }

    .section-title {
        color: #fff;
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
        text-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #007bff, transparent);
    }

    .card {
        border: none;
        border-radius: 50%;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3),
                    0 0 30px rgba(0, 123, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
        opacity: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
    }

    .card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(0deg);
        transition: transform 0.5s ease;
    }

    .card:hover {
        transform: scale(1.08) translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4),
                    0 0 50px rgba(0, 123, 255, 0.3);
    }

    .card:hover::before {
        transform: rotate(180deg);
    }

    .latest-trailers .card,
    .latest-movies .card {
        width: 280px;
        height: 280px;
        margin: 0 auto;
    }

    .card-img-top {
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        transition: transform 0.5s ease;
    }

    .card:hover .card-img-top {
        transform: scale(1.1);
    }

    .card-body {
        position: absolute;
        bottom: -100%;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.85);
        padding: 20px;
        color: white;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        backdrop-filter: blur(5px);
    }

    .card:hover .card-body {
        bottom: 0;
    }

    .card-title {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #fff;
        text-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .card-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .btn-primary {
        background: linear-gradient(45deg, #007bff, #00bfff);
        border: none;
        padding: 8px 20px;
        font-size: 0.9rem;
        border-radius: 20px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        overflow: hidden;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        background: linear-gradient(45deg, #00bfff, #007bff);
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .btn-primary:hover::before {
        transform: rotate(225deg);
    }

    @keyframes dropBubble {
        0% {
            transform: translateY(-300px) rotate(-15deg);
            opacity: 0;
        }
        50% {
            transform: translateY(30px) rotate(5deg);
            opacity: 0.8;
        }
        75% {
            transform: translateY(-15px) rotate(-5deg);
            opacity: 0.9;
        }
        100% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
    }

    .bubble-animation {
        animation: dropBubble 1.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    .latest-trailers,
    .latest-movies {
        min-height: 400px;
        padding: 70px 0;
        position: relative;
    }

    .latest-movies {
        background: linear-gradient(135deg, #0c0e10 0%, #1a1c20 100%);
    }

    /* Pagination styling */
    .pagination {
        margin-top: 2rem;
    }

    .pagination .page-link {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        margin: 0 5px;
        border-radius: 20px;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: rgba(0, 123, 255, 0.3);
        color: #fff;
        transform: translateY(-2px);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #007bff, #00bfff);
        border: none;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }

    /* Add floating bubbles in the background */
    .background-bubbles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
        pointer-events: none;
    }

    .background-bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
        animation: float 20s linear infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(100vh) scale(0);
            opacity: 0;
        }
        50% {
            opacity: 0.3;
        }
        100% {
            transform: translateY(-100vh) scale(1);
            opacity: 0;
        }
    }

    /* Fix for Latest Movies visibility */
    .movie-bubble {
        opacity: 1 !important;
        animation: none;
    }
    
    .movie-bubble.animate {
        animation: dropBubble 1.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    /* Ensure cards are visible by default */
    .latest-movies .card {
        opacity: 1;
    }
</style>

<div class="background-bubbles">
    <?php for($i = 0; $i < 10; $i++): ?>
        <div class="background-bubble" 
             style="left: <?= rand(0, 100) ?>%; 
                    width: <?= rand(20, 100) ?>px; 
                    height: <?= rand(20, 100) ?>px; 
                    animation-delay: <?= rand(0, 10) ?>s;
                    animation-duration: <?= rand(15, 25) ?>s;">
        </div>
    <?php endfor; ?>
</div>

<div class="container-fluid latest-trailers">
    <div class="container">
        <h2 class="section-title">Latest Trailers</h2>
        <div class="row justify-content-center">
            <?php
            // Fetch the 3 latest movies
            $stmt = $pdo->query("SELECT * FROM movies ORDER BY release_date DESC LIMIT 3");
            $delay = 0;
            while ($movie = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $imagePath = $movie['thumbnail_url'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card" style="animation-delay: <?= $delay ?>s;">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>"
                         onerror="this.onerror=null;this.src='<?= $imagePath ?>';" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                        <a href="movie.php?id=<?= $movie['id'] ?>" class="btn btn-primary btn-sm">Watch Trailer</a>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 0.3;
            } 
            ?>
        </div>
    </div>
</div>

<div class="container-fluid latest-movies">
    <div class="container">
        <h2 class="section-title">Latest Movies</h2>
        <div class="row justify-content-center">
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
            $delay = 0;
            while ($movie = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $imagePath = $movie['thumbnail_url'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card" style="animation-delay: <?= $delay ?>s;">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>"
                         onerror="this.onerror=null;this.src='<?= $imagePath ?>';" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                        <p class="card-text"><?= substr(htmlspecialchars($movie['description']), 0, 100) ?>...</p>
                        <a href="movie.php?id=<?= $movie['id'] ?>" class="btn btn-primary btn-sm">Watch Trailer</a>
                    </div>
                </div>
            </div>
            <?php 
                $delay += 0.2;
            } 
            ?>
        </div>
    </div>

    <!-- Pagination controls -->
    <nav aria-label="Page navigation" class="mt-4">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for Latest Trailers
        const trailerCards = document.querySelectorAll('.latest-trailers .card');
        trailerCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.3}s`;
            card.classList.add('bubble-animation');
        });
        
        // Animation for Latest Movies
        const movieCards = document.querySelectorAll('.latest-movies .card');
        movieCards.forEach((card, index) => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        card.style.animationDelay = `${index * 0.2}s`;
                        card.classList.add('bubble-animation');
                        observer.unobserve(card);
                    }
                });
            }, { threshold: 0.1 });
            
            observer.observe(card);
        });

        // Add hover effect for buttons
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                btn.style.setProperty('--x', `${x}px`);
                btn.style.setProperty('--y', `${y}px`);
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

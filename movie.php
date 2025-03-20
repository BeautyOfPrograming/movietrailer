<?php 
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: /");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$_GET['id']]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: /");
    exit;
}

function getAparatVideoUrl($url) {
    $html = file_get_contents($url);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $videoUrl = '';
    $videoTags = $dom->getElementsByTagName('video');
    foreach ($videoTags as $videoTag) {
        $sourceTags = $videoTag->getElementsByTagName('source');
        foreach ($sourceTags as $sourceTag) {
            $videoUrl = $sourceTag->getAttribute('src');
            break;
        }
        if (!empty($videoUrl)) {
            break;
        }
    }
    return $videoUrl;
}

function convertToEmbedUrl($url) {
    if (preg_match('/youtu\.?be\/(.*?)(\?|$)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    } elseif (preg_match('/youtube\.com\/watch\?v=(.*?)(\&|$)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    } elseif (preg_match('/vimeo\.com\/(.*?)(\?|$)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    } elseif (preg_match('/dailymotion\.com\/video\/(.*?)(\?|$)/', $url, $matches)) {
        return 'https://www.dailymotion.com/embed/video/' . $matches[1];
    } elseif (strpos($url, 'aparat.com') !== false) {
        return getAparatVideoUrl($url);
    }
    return $url;
}

$embedUrl = convertToEmbedUrl($movie['trailer_url']);
?>

<style>
    body {
        background: linear-gradient(135deg, #1a1c20 0%, #0c0e10 100%);
        color: white;
        min-height: 100vh;
        overflow-x: hidden;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 50% 50%, rgba(0,123,255,0.1) 0%, transparent 50%);
        z-index: 0;
        pointer-events: none;
    }

    /* Add floating background bubbles */
    .background-bubbles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

    .movie-header { 
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                    url('<?= $movie['thumbnail_url'] ?>') center/cover;
        padding: 150px 0;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .movie-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, transparent 0%, rgba(0,0,0,0.8) 100%);
        z-index: 1;
    }

    .movie-header .container {
        position: relative;
        z-index: 2;
    }

    .movie-title {
        font-size: 4rem;
        font-weight: 800;
        text-shadow: 0 0 30px rgba(0, 123, 255, 0.5);
        margin-bottom: 20px;
        animation: fadeInUp 1s ease;
        background: linear-gradient(45deg, #fff, #007bff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: 1px;
    }

    .movie-meta {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.3rem;
        animation: fadeInUp 1s ease 0.2s backwards;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .movie-meta i {
        color: #007bff;
        text-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
    }

    .video-container {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3),
                    0 0 20px rgba(0, 123, 255, 0.2);
        margin-bottom: 30px;
        animation: fadeInUp 1s ease 0.4s backwards;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .video-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        pointer-events: none;
    }

    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 15px;
    }

    .movie-details {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3),
                    0 0 20px rgba(0, 123, 255, 0.2);
        animation: fadeInUp 1s ease 0.6s backwards;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .movie-details h3 {
        font-size: 1.8rem;
        margin-bottom: 25px;
        color: #fff;
        text-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
        position: relative;
        padding-bottom: 15px;
    }

    .movie-details h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #007bff, transparent);
        border-radius: 3px;
    }

    .movie-description {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 30px;
        padding: 25px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        animation: fadeInUp 1s ease 0.5s backwards;
    }

    .detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .detail-item:hover {
        transform: translateX(10px);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .detail-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #007bff, #00bfff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 1.4rem;
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
    }

    .detail-item:hover .detail-icon {
        transform: scale(1.1);
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .detail-value {
        color: white;
        font-size: 1.2rem;
        font-weight: 500;
        text-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .back-button {
        position: fixed;
        top: 20px;
        left: 20px;
        background: rgba(0, 123, 255, 0.2);
        border: 2px solid rgba(0, 123, 255, 0.5);
        color: white;
        padding: 12px 25px;
        border-radius: 30px;
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 1000;
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .back-button:hover {
        background: rgba(0, 123, 255, 0.4);
        transform: translateX(-5px);
        color: white;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
    }

    .back-button i {
        transition: transform 0.3s ease;
    }

    .back-button:hover i {
        transform: translateX(-3px);
    }

    /* Add responsive adjustments */
    @media (max-width: 768px) {
        .movie-title {
            font-size: 2.5rem;
        }
        .movie-header {
            padding: 100px 0;
        }
        .movie-details {
            margin-top: 30px;
        }
    }

    /* Add these new styles after the existing background-bubbles styles */
    .flower-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .flower {
        position: absolute;
        width: 20px;
        height: 20px;
        background: url('assets/images/thumbnails/lily.png') center/contain no-repeat;
        opacity: 0;
        animation: flowerFloat 15s linear infinite;
        filter: drop-shadow(0 0 5px rgba(0, 123, 255, 0.5));
    }

    .flower::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(0,123,255,0.2) 0%, transparent 70%);
        filter: blur(3px);
        animation: flowerGlow 3s ease-in-out infinite alternate;
    }

    @keyframes flowerFloat {
        0% {
            transform: translate(0, 100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 0.8;
        }
        90% {
            opacity: 0.8;
        }
        100% {
            transform: translate(var(--x-end), -100px) rotate(var(--rotation));
            opacity: 0;
        }
    }

    @keyframes flowerGlow {
        0% {
            transform: scale(1);
            opacity: 0.5;
        }
        100% {
            transform: scale(1.2);
            opacity: 0.8;
        }
    }

    /* Add a magical sparkle effect */
    .sparkle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #fff;
        border-radius: 50%;
        animation: sparkleFloat 8s linear infinite;
        opacity: 0;
    }

    @keyframes sparkleFloat {
        0% {
            transform: translate(0, 100vh) scale(0);
            opacity: 0;
        }
        20% {
            opacity: 1;
        }
        80% {
            opacity: 1;
        }
        100% {
            transform: translate(var(--x-end), -100px) scale(1);
            opacity: 0;
        }
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

<div class="flower-container">
    <?php for($i = 0; $i < 20; $i++): ?>
        <div class="flower" 
             style="left: <?= rand(0, 100) ?>%; 
                    animation-delay: <?= rand(0, 10) ?>s;
                    --x-end: <?= rand(-100, 100) ?>px;
                    --rotation: <?= rand(-360, 360) ?>deg;
                    transform: scale(<?= rand(0.5, 1.5) ?>);">
        </div>
        <div class="sparkle"
             style="left: <?= rand(0, 100) ?>%;
                    animation-delay: <?= rand(0, 10) ?>s;
                    --x-end: <?= rand(-50, 50) ?>px;
                    background: <?= rand(0, 1) ? 'rgba(0,123,255,0.8)' : 'rgba(255,255,255,0.8)' ?>;
                    box-shadow: 0 0 10px <?= rand(0, 1) ? 'rgba(0,123,255,0.5)' : 'rgba(255,255,255,0.5)' ?>;">
        </div>
    <?php endfor; ?>
</div>

<a href="index.php" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Movies
</a>

<div class="movie-header">
    <div class="container">
        <h1 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h1>
        <div class="movie-meta">
            <i class="fas fa-calendar-alt"></i> <?= date('F j, Y', strtotime($movie['release_date'])) ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="video-container">
                <div class="video-wrapper">
            <?php if (strpos($embedUrl, 'youtube.com') !== false): ?>
                    <iframe src="<?= $embedUrl ?>" title="Trailer" frameborder="0" allowfullscreen></iframe>
            <?php elseif (strpos($embedUrl, 'aparat.com') !== false): ?>
                        <video controls autoplay muted>
                    <source src="<?= $embedUrl ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                        <video controls autoplay muted>
                    <source src="<?= $embedUrl ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>
                </div>
            </div>

            <div class="movie-description">
                <?= nl2br(htmlspecialchars($movie['description'])) ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="movie-details">
                <h3 class="mb-4">Movie Details</h3>
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Genre</div>
                        <div class="detail-value"><?= htmlspecialchars($movie['title']) ?></div>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Release Date</div>
                        <div class="detail-value"><?= date('F j, Y', strtotime($movie['release_date'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create dynamic flowers
    const flowerContainer = document.querySelector('.flower-container');
    const createFlower = () => {
        const flower = document.createElement('div');
        flower.className = 'flower';
        flower.style.left = Math.random() * 100 + '%';
        flower.style.animationDelay = Math.random() * 10 + 's';
        flower.style.setProperty('--x-end', (Math.random() * 200 - 100) + 'px');
        flower.style.setProperty('--rotation', (Math.random() * 720 - 360) + 'deg');
        flower.style.transform = `scale(${Math.random() * 0.5 + 0.5})`;
        flowerContainer.appendChild(flower);

        // Remove flower after animation
        flower.addEventListener('animationend', () => {
            flower.remove();
        });
    };

    // Create flowers periodically
    setInterval(createFlower, 2000);

    // Create sparkles
    const createSparkle = () => {
        const sparkle = document.createElement('div');
        sparkle.className = 'sparkle';
        sparkle.style.left = Math.random() * 100 + '%';
        sparkle.style.animationDelay = Math.random() * 10 + 's';
        sparkle.style.setProperty('--x-end', (Math.random() * 100 - 50) + 'px');
        sparkle.style.background = Math.random() > 0.5 ? 'rgba(0,123,255,0.8)' : 'rgba(255,255,255,0.8)';
        sparkle.style.boxShadow = Math.random() > 0.5 ? '0 0 10px rgba(0,123,255,0.5)' : '0 0 10px rgba(255,255,255,0.5)';
        flowerContainer.appendChild(sparkle);

        // Remove sparkle after animation
        sparkle.addEventListener('animationend', () => {
            sparkle.remove();
        });
    };

    // Create sparkles periodically
    setInterval(createSparkle, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>
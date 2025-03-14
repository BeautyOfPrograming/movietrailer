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
?>

<?php



function getAparatVideoUrl($url) {
    // Fetch the HTML content of the Aparat page
    $html = file_get_contents($url);

    // Load the HTML content into DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Find the video source URL
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
    }elseif (strpos($url, 'aparat.com') !== false) {
        return getAparatVideoUrl($url);
    }



    return $url; // Return the original URL if it's not a supported video link
}

$embedUrl = convertToEmbedUrl($movie['trailer_url']);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h1><?= $movie['title'] ?></h1>
           
            <?php if (strpos($embedUrl, 'youtube.com') !== false): ?>
                <div class="ratio ratio-16x9">
                    <iframe src="<?= $embedUrl ?>" title="Trailer" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php elseif (strpos($embedUrl, 'aparat.com') !== false): ?>
                <video controls width="100%" height="auto" autoplay muted>
                    <source src="<?= $embedUrl ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <video controls width="100%" height="auto" autoplay muted>
                    <source src="<?= $embedUrl ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>

            <h3 class="mt-4">Description</h3>
            <p><?= $movie['description'] ?></p>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Details</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Genre: <?= $movie['title'] ?></li>
                        <li class="list-group-item">Release Date: <?= date('F j, Y', strtotime($movie['release_date'])) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
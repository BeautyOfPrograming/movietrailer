<?php

namespace MovieTrailer;

use Google_Client;
use Google_Service_YouTube;
use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TrailerScraper {
    private $youtube;
    private $pdo;
    private $logger;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->setupLogger();
        $this->setupYouTubeClient();
    }

    private function setupLogger() {
        $this->logger = new Logger('trailer_scraper');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/scraper.log', Logger::INFO));
    }

    private function setupYouTubeClient() {
        $client = new Google_Client();
        $client->setDeveloperKey($_ENV['YOUTUBE_API_KEY']);
        $this->youtube = new Google_Service_YouTube($client);
    }

    public function scrapeLatestTrailers($count = 5) {
        try {
            $searchResponse = $this->youtube->search->listSearch('snippet', [
                'q' => 'official movie trailer',
                'type' => 'video',
                'videoDuration' => 'short',
                'maxResults' => $count,
                'order' => 'date'
            ]);

            foreach ($searchResponse->getItems() as $searchResult) {
                $videoId = $searchResult->getId()->getVideoId();
                $title = $searchResult->getSnippet()->getTitle();
                $description = $searchResult->getSnippet()->getDescription();
                $thumbnail = $searchResult->getSnippet()->getThumbnails()->getHigh()->getUrl();
                $publishedAt = $searchResult->getSnippet()->getPublishedAt();

                // Clean the title to extract movie name
                $movieName = $this->cleanMovieTitle($title);
                
                $this->saveToDatabase([
                    'title' => $movieName,
                    'description' => $description,
                    'trailer_url' => "https://www.youtube.com/watch?v=" . $videoId,
                    'thumbnail_url' => $thumbnail,
                    'release_date' => date('Y-m-d', strtotime($publishedAt))
                ]);
            }

            $this->logger->info("Successfully scraped {$count} trailers");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Error scraping trailers: " . $e->getMessage());
            throw $e;
        }
    }

    private function cleanMovieTitle($title) {
        // Remove common trailer-related phrases
        $patterns = [
            '/official trailer/i',
            '/teaser trailer/i',
            '/official teaser/i',
            '/\([\d]{4}\)/',
            '/\|.*$/',
            '/HD/',
            '/4K/',
        ];
        $title = preg_replace($patterns, '', $title);
        return trim($title);
    }

    private function saveToDatabase($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO movies (title, description, trailer_url, thumbnail_url, release_date, created_at)
                VALUES (:title, :description, :trailer_url, :thumbnail_url, :release_date, NOW())
                ON DUPLICATE KEY UPDATE
                description = VALUES(description),
                trailer_url = VALUES(trailer_url),
                thumbnail_url = VALUES(thumbnail_url),
                release_date = VALUES(release_date),
                updated_at = NOW()
            ");
            
            $stmt->execute($data);
            $this->logger->info("Saved trailer: {$data['title']}");
        } catch (\Exception $e) {
            $this->logger->error("Error saving trailer: " . $e->getMessage());
            throw $e;
        }
    }
} 
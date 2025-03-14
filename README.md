# Movie Trailer Scraper

This tool automatically scrapes the latest movie trailers from YouTube and adds them to your database.

## Setup

1. Install dependencies:
```bash
composer install
```

2. Copy the environment file and configure it:
```bash
cp .env.example .env
```

3. Edit `.env` and add your YouTube API key. You can get one from the [Google Cloud Console](https://console.cloud.google.com/):
   - Enable the YouTube Data API v3
   - Create credentials (API key)
   - Add the key to your `.env` file

4. Make sure your database has the required table structure:
```sql
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    trailer_url VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    release_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_trailer (trailer_url)
);
```

## Usage

Run the scraper:
```bash
php scrape_trailers.php
```

The script will:
- Fetch the 5 latest movie trailers from YouTube
- Clean and process the data
- Save to your database
- Log all activities to `logs/scraper.log`

## Automation

To automate the scraping process, add a cron job:

```bash
# Run every 6 hours
0 */6 * * * cd /path/to/your/project && php scrape_trailers.php
``` 
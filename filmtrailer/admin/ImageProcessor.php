<?php
class ImageProcessor {
    private $source;
    private $image;
    private $type;

    public function __construct($filePath) {
        $this->source = $filePath;
        list($this->width, $this->height, $this->type) = getimagesize($filePath);

        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_WEBP:
                $this->image = imagecreatefromwebp($filePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }
    }

    public function resize($newWidth, $newHeight) {
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        imagecopyresampled(
            $resized, $this->image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $this->width, $this->height
        );
        
        $this->image = $resized;
        return $this;
    }

    public function save($destination, $quality = 80) {
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $destination, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $destination, 9);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($this->image, $destination, $quality);
                break;
        }
        imagedestroy($this->image);
    }
}
?>
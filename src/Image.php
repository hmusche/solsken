<?php

namespace Solsken;

/**
 * Image class for operations on Images
 */
class Image {
    /**
     * Resize given image and save in new location
     * @var string $oldFile     path and filename of image to resize
     * @var string $newFile     path and filename of resized image
     * @var int    $width       Width of new image in pixels
     * @var int    $height      Height of new image in pixels, or retain current ratio if unprovided
     * @return bool             True if operation was successful
     */
    static public function resize($oldFile, $newFile, $width, $height = null) {
        if (!file_exists($oldFile)) {
            throw new \Exception("File $oldFile not found, can't resize");
        }

        list($w, $h, $type) = getimagesize($oldFile);
        $ratio = $w / $h;

        if ($w < $width) {
            // can't resize
            return false;
        }

        if ($height === null) {
            $height = $width / $ratio;
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($oldFile);
                break;

            case IMAGETYPE_PNG;
                $src = imagecreatefrompng($oldFile);
                break;

            case IMAGETYPE_GIF;
                $src = imagecreatefromgif($oldFile);
                break;
        }

        /**
         * Get orientation from exif data
         */
        $exif = exif_read_data($oldFile);

        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $src = imagerotate($src, 180, 0);
                    break;

                case 6:
                    $src = imagerotate($src, -90, 0);
                    $tmp = $width;
                    $width = $height;
                    $height = $tmp;

                    $tmp = $w;
                    $w = $h;
                    $h = $tmp;
                    break;

                case 8:
                    $src = imagerotate($src, 90, 0);
                    $tmp = $width;
                    $width = $height;
                    $height = $tmp;

                    $tmp = $w;
                    $w = $h;
                    $h = $tmp;
                    break;
            }
        }

        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $w, $h);

        if (!file_exists(dirname($newFile))) {
            mkdir(dirname($newFile), 0755, true);
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dst, $newFile, 95);
                break;

            case IMAGETYPE_PNG;
                imagepng($dst, $newFile);
                break;

            case IMAGETYPE_GIF;
                imagegif($dst, $newFile);
                break;
        }

        return true;
    }
}

<?php

namespace Solsken;

class Image {
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

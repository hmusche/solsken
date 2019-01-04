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

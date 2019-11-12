<?php

namespace Solsken;

/**
 * Image class for operations on Images
 */
class Image {
    const RESIZE_MODE_PAD = 1;
    const RESIZE_MODE_CROP = 2;
    const RESIZE_MODE_STRETCH = 3;

    const RESIZE_ANCHOR_CENTER = 1;
    const RESIZE_ANCHOR_TOP = 2;
    const RESIZE_ANCHOR_BOTTOM = 3;
    const RESIZE_ANCHOR_RIGHT = 4;
    const RESIZE_ANCHOR_LEFT = 5;

    protected $_nativeInfo = [
        'width'  => 0,
        'height' => 0,
        'ratio'  => 1,
        'type'   => ''
    ];

    protected $_resizeMode = self::RESIZE_MODE_PAD;
    protected $_resizeAnchor = self::RESIZE_ANCHOR_CENTER;

    protected $_goalInfo = [];

    protected $_image;
    protected $_imagePath;

    public function loadFile($imagePath) {
        if (!file_exists($imagePath)) {
            throw new \Exception("Can't load file $imagePath");
        }

        $this->_imagePath = $imagePath;

        $this->_getNativeInfo();
        $this->_loadImage();

        return $this;
    }

    public function setResizeMode($mode) {
        $this->_resizeMode = $mode;

        return $this;
    }

    public function setResizeAnchor($anchor) {
        $this->_resizeAnchor = $anchor;

        return $this;
    }

    protected function _getNativeInfo() {
        list($w, $h, $type) = getimagesize($this->_imagePath);
        $ratio = $w / $h;

        $this->_nativeInfo['width']  = $w;
        $this->_nativeInfo['height'] = $h;
        $this->_nativeInfo['type']   = $type;
        $this->_nativeInfo['ratio']  = $w / $h;
    }

    protected function _loadImage() {
        switch ($this->_nativeInfo['type']) {
            case IMAGETYPE_JPEG:
                $this->_image = imagecreatefromjpeg($this->_imagePath);
                break;

            case IMAGETYPE_PNG;
                $this->_image = imagecreatefrompng($this->_imagePath);
                break;

            case IMAGETYPE_GIF;
                $this->_image = imagecreatefromgif($this->_imagePath);
                break;
        }
    }

    public function rotateFromExif() {
        /**
         * Get orientation from exif data
         */
        $exif = exif_read_data($this->_imagePath);

        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $this->_image = imagerotate($this->_image, 180, 0);
                    break;

                case 6:
                    $this->_image = imagerotate($this->_image, -90, 0);

                    $tmp = $this->_nativeInfo['width'];
                    $this->_nativeInfo['width'] = $this->_nativeInfo['height'];
                    $this->_nativeInfo['height'] = $tmp;
                    $this->_nativeInfo['ratio']  = $this->_nativeInfo['width'] / $this->_nativeInfo['height'];

                    break;

                case 8:
                    $this->_image = imagerotate($this->_image, 90, 0);

                    $tmp = $this->_nativeInfo['width'];
                    $this->_nativeInfo['width'] = $this->_nativeInfo['height'];
                    $this->_nativeInfo['height'] = $tmp;
                    $this->_nativeInfo['ratio']  = $this->_nativeInfo['width'] / $this->_nativeInfo['height'];
                    break;
            }
        }

        return $this;
    }

    public function setGoalInfo($goalInfo = []) {
        $this->_goalInfo = array_merge($this->_nativeInfo, $goalInfo);

        $missingInfo = '';

        if (isset($goalInfo['width']) && !isset($goalInfo['height'])) {
            $this->_goalInfo['height'] = $this->_goalInfo['width'] / $this->_goalInfo['ratio'];
        } elseif (isset($goalInfo['height']) && !isset($goalInfo['width'])) {
            $this->_goalInfo['width'] = $this->_goalInfo['height'] * $this->_goalInfo['ratio'];
        } elseif (!isset($goalInfo['ratio'])) {
            $this->_goalInfo['ratio'] = $this->_goalInfo['width'] / $this->_goalInfo['height'];
        }

        return $this;
    }

    protected function _getResizeOptions() {
        $resizeOptions = [
            'dst_crop_x' => 0,
            'dst_crop_y' => 0,
            'src_crop_x' => 0,
            'src_crop_y' => 0,
            'dst_width'  => $this->_goalInfo['width'],
            'dst_height' => $this->_goalInfo['height'],
            'src_width'  => $this->_nativeInfo['width'],
            'src_height' => $this->_nativeInfo['height']
        ];

        switch ($this->_resizeMode) {
            case self::RESIZE_MODE_PAD:
                if ($this->_nativeInfo['ratio'] != $this->_goalInfo['ratio']) {
                    if ($this->_nativeInfo['ratio'] < 1) {
                        //height > width
                        $resizeOptions['src_crop_y'] = ceil(($this->_nativeInfo['height'] - $this->_nativeInfo['width']) / 2);
                        $resizeOptions['dst_height'] = $this->_goalInfo['width'] / $this->_nativeInfo['ratio'];
                    } else {
                        $resizeOptions['src_crop_x'] = ceil(($this->_nativeInfo['width'] - $this->_nativeInfo['height']) / 2);
                        $resizeOptions['dst_width'] = $this->_goalInfo['height'] * $this->_nativeInfo['ratio'];
                    }
                }

                break;

            case self::RESIZE_MODE_STRETCH:
                break;

            case self::RESIZE_MODE_CROP:
                break;

        }

        return $resizeOptions;
    }

    public function resizeImage() {
        $dst = imagecreatetruecolor($this->_goalInfo['width'], $this->_goalInfo['height']);

        $resizeOptions = $this->_getResizeOptions();

        imagecopyresampled(
            $dst,
            $this->_image,
            $resizeOptions['dst_crop_x'],
            $resizeOptions['dst_crop_y'],
            $resizeOptions['src_crop_x'],
            $resizeOptions['src_crop_y'],
            $resizeOptions['dst_width'],
            $resizeOptions['dst_height'],
            $resizeOptions['src_width'],
            $resizeOptions['src_height']
        );

        $this->_image = $dst;
    }

    public function saveImage($path) {
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        switch ($this->_goalInfo['type']) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->_image, $path, 95);
                break;

            case IMAGETYPE_PNG;
                imagepng($this->_image, $path);
                break;

            case IMAGETYPE_GIF;
                imagegif($this->_image, $path);
                break;

            default:
                return false;
                break;
        }

        return true;

    }


    /**
     * Resize given image and save in new location
     * @var string $oldFile     path and filename of image to resize
     * @var string $newFile     path and filename of resized image
     * @var int    $width       Width of new image in pixels
     * @var int    $height      Height of new image in pixels, or retain current ratio if unprovided
     * @return bool             True if operation was successful
     */
    static public function resize($oldFile, $newFile, $width, $height = null) {
        $instance = new self();

        $instance->loadFile($oldFile);

        $targetInfo = ['width' => $width];

        if ($height) {
            $targetInfo['height'] = $height;
            $targetInfo['ratio']  = $width / $height;
        }

        $instance->rotateFromExif();
        $instance->setGoalInfo($targetInfo);
        $instance->resizeImage();

        return $instance->saveImage($newFile);
    }
}

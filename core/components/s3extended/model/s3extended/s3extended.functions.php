<?php
//////////////////////////////////////////////////////////////
///  S3 Extended by Robin Rijkeboer <robin@qaraqter.nl>     //
//   available at https://github.com/Qaraqter/s3extended   ///
//////////////////////////////////////////////////////////////
///                                                         //
// s3extended.functions.php - general functions class       //
//                                                         ///
//////////////////////////////////////////////////////////////
class s3extended_functions {
    static function generateFileManagerThumbs($url, $thumbWidth, $thumbHeight, $quality) {
        $assetsPath = MODX_ASSETS_PATH . "components/s3extended";
        $pathinfo = pathinfo($url);
        $ext = strtolower($pathinfo['extension']);
        $file = $pathinfo['basename'];
        $tempFile = $assetsPath . "/tmp/" . $file;
        $cacheFileName = md5($url) . $thumbWidth . "x" . $thumbHeight . "." . $ext;
        $cacheName = $assetsPath . "/cache/" . $cacheFileName;
        $cacheUrl = MODX_ASSETS_URL . "components/s3extended/cache/" . $cacheFileName;
        $url = str_replace(" ", "%20", $url);
        if (file_exists($cacheName)) {
            return $cacheUrl;
        }
        if (!file_exists($assetsPath . "/tmp")) {
            $oldmask = umask(0);
            mkdir($assetsPath . "/tmp", 0777, true);
            umask($oldmask);
        }
        if (!file_exists($assetsPath . "/cache")) {
            $oldmask = umask(0);
            mkdir($assetsPath . "/cache", 0777, true);
            umask($oldmask);
        }
        
        if (file_put_contents($tempFile, fopen($url, 'r')) === false) {
            return 0;
        }
        
        // Get new sizes
        list($width, $height) = getimagesize($tempFile);
            // Load
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            switch ($ext) {
                case "gif":
                    $source = imagecreatefromgif($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagegif($thumb, $cacheName, $quality);
                    break;
                case "jpeg":
                    $source = imagecreatefromjpeg($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagejpeg($thumb, $cacheName, $quality);
                    break;
                case "jpg":
                    $source = imagecreatefromjpeg($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagejpeg($thumb, $cacheName, $quality);
                    break;
                case "png":
                    $source = imagecreatefrompng($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagepng($thumb, $cacheName, $quality);
                    break;
                default:
                    //$this->xpdo->log(modX::LOG_LEVEL_ERROR, "[" . $this->getTypeName() . "] " . $this->xpdo->lexicon('s3extended.notImplemented') . " File: " . $file['name']);
                    break;
            }
        unlink($tempFile);
        return $cacheUrl;
    }
}

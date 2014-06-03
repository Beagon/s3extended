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

    /** @var class $s3 */
    private $s3;

    public function __construct($s3) {
       $this->s3 = $s3;
    }
    public function generateFileManagerThumbs($url) {
        $imageWidth = $this->s3->ctx->getOption('filemanager_image_width', 400);
        $imageHeight = $this->s3->ctx->getOption('filemanager_image_height', 300);
        $thumbHeight = $this->s3->ctx->getOption('filemanager_thumb_height', 60);
        $thumbWidth = $this->s3->ctx->getOption('filemanager_thumb_width', 80);
        $thumbnailQuality = $this->s3->getOption('thumbnailQuality', $this->s3->properties, 90);
        $assetsPath = MODX_ASSETS_PATH . "components/s3extended";
        $pathinfo = pathinfo($url);
        $ext = strtolower($pathinfo['extension']);
        $file = $pathinfo['basename'];
        $tempFile = $assetsPath . "/tmp/" . $file;
        $cacheUrls = array();
        //Thumbnail
        $cacheFileName = md5($url) . $thumbWidth . "x" . $thumbHeight . "." . $ext;
        $cacheName = $assetsPath . "/cache/" . $cacheFileName;
        $cacheUrl = MODX_ASSETS_URL . "components/s3extended/cache/" . $cacheFileName;
        array_push($cacheUrls, $cacheUrl);
        //SmallerImage
        $cacheFileName2 = md5($url) . $imageWidth . "x" . $imageHeight . "." . $ext;
        $cacheName2 = $assetsPath . "/cache/" . $cacheFileName2;
        $cacheUrl2 = MODX_ASSETS_URL . "components/s3extended/cache/" . $cacheFileName2;
        array_push($cacheUrls, $cacheUrl2);
        $url = str_replace(" ", "%20", $url);

        if (file_exists($cacheName) && file_exists($cacheName2)) {
            array_push($cacheUrls, $cacheUrl);
            array_push($cacheUrls, $cacheUrl2);
            return $cacheUrls;
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
            $thumb2 = imagecreatetruecolor($imageWidth, $imageHeight);
            switch ($ext) {
                case "gif":
                    $source = imagecreatefromgif($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagecopyresampled($thumb2, $source, 0, 0, 0, 0, $imageWidth, $imageHeight, $width, $height);
                    imagegif($thumb, $cacheName, $thumbnailQuality);
                    imagegif($thumb2, $cacheName2, $thumbnailQuality);
                    break;
                case "jpeg":
                    $source = imagecreatefromjpeg($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagecopyresampled($thumb2, $source, 0, 0, 0, 0, $imageWidth, $imageHeight, $width, $height);
                    imagejpeg($thumb, $cacheName, $thumbnailQuality);
                    imagejpeg($thumb2, $cacheName2, $thumbnailQuality);
                    break;
                case "jpg":
                    $source = imagecreatefromjpeg($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagecopyresampled($thumb2, $source, 0, 0, 0, 0, $imageWidth, $imageHeight, $width, $height);
                    imagejpeg($thumb, $cacheName, $thumbnailQuality);
                    imagejpeg($thumb2, $cacheName2, $thumbnailQuality);
                    break;
                case "png":
                    $source = imagecreatefrompng($tempFile);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagecopyresampled($thumb2, $source, 0, 0, 0, 0, $imageWidth, $imageHeight, $width, $height);
                    imagepng($thumb, $cacheName, $thumbnailQuality);
                    imagepng($thumb2, $cacheName2, $thumbnailQuality);
                    break;
                default:
                    //$this->xpdo->log(modX::LOG_LEVEL_ERROR, "[" . $this->getTypeName() . "] " . $this->xpdo->lexicon('s3extended.notImplemented') . " File: " . $file['name']);
                    break;
            }
        unlink($tempFile);
        return $cacheUrls;
    }
}

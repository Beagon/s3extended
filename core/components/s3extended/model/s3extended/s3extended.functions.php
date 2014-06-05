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
        $s3CacheFolder = $this->s3->getOption('s3CacheFolder', $this->s3->properties, '_cache');
        $properties = $this->s3->getPropertyList();
        $s3CacheFolderURL = $properties['url']. $s3CacheFolder . "/";
        $cacheToS3 = $this->s3->getOption('cacheToS3', $this->s3->properties,'Yes');

        $imageWidth = $this->s3->ctx->getOption('filemanager_image_width', 400);
        $imageHeight = $this->s3->ctx->getOption('filemanager_image_height', 300);
        $thumbHeight = $this->s3->ctx->getOption('filemanager_thumb_height', 60);
        $thumbWidth = $this->s3->ctx->getOption('filemanager_thumb_width', 80);
        $thumbnailQuality = $this->s3->getOption('thumbnailQuality', $this->s3->properties, 90);

        $assetsPath = MODX_ASSETS_PATH . "components/s3extended";
        $pathinfo = pathinfo($url);
        $ext = strtolower($pathinfo['extension']);
        $tempFile = $assetsPath . "/tmp/" . uniqid();
        $cacheUrls = array();
        //Thumbnail
        $cacheFileName = md5($url) . $thumbWidth . "x" . $thumbHeight . "." . $ext;
        $cacheName = $assetsPath . "/cache/" . $cacheFileName;
        $cacheUrl = MODX_ASSETS_URL . "components/s3extended/cache/" . $cacheFileName;

        //SmallerImage
        $cacheFileName2 = md5($url) . $imageWidth . "x" . $imageHeight . "." . $ext;
        $cacheName2 = $assetsPath . "/cache/" . $cacheFileName2;
        $cacheUrl2 = MODX_ASSETS_URL . "components/s3extended/cache/" . $cacheFileName2;
        $url = str_replace(" ", "%20", $url);

        if ($cacheToS3 == "Yes"
            && $this->s3->driver->if_object_exists($this->s3->bucket, $s3CacheFolder . "/" . $cacheFileName)
            && $this->s3->driver->if_object_exists($this->s3->bucket, $s3CacheFolder . "/" . $cacheFileName2)) {
            array_push($cacheUrls, $s3CacheFolderURL . $cacheFileName);
            array_push($cacheUrls, $s3CacheFolderURL . $cacheFileName2);
            return $cacheUrls;
        }

        if (file_exists($cacheName) && file_exists($cacheName2)) {
            if ($cacheToS3 == "Yes") {
                if($this->uploadFile("", $s3CacheFolder, $cacheFileName, $cacheName)) {
                    $cacheUrl = $s3CacheFolderURL . $cacheFileName;
                    unlink($cacheName);
                }
                if($this->uploadFile("", $s3CacheFolder, $cacheFileName2, $cacheName2)) {
                    $cacheUrl2 = $s3CacheFolderURL . $cacheFileName2;
                    unlink($cacheName2);
                }
            }
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
        
        list($width, $height) = getimagesize($tempFile);
        
        if ($width > $height) {
            $onePercent = $width / 100;
            $downSizePercentage = floatval(($imageWidth / $onePercent)) / 100;
            $imageHeight = $height * $downSizePercentage;
        } else {
            $onePercent = $width / 100;
            $downSizePercentage = floatval(($imageHeight / $onePercent)) / 100;
            $imageWidth = $width * $downSizePercentage;
        }
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
                    $this->s3->xpdo->log(modX::LOG_LEVEL_ERROR, "[" . $this->s3->getTypeName() . "] " . $this->s3->xpdo->lexicon('s3extended.notImplemented') . " File: " . $pathinfo['basename']);
                    break;
            }
        unlink($tempFile);
        if ($cacheToS3 == "Yes") {
            if($this->uploadFile("", $s3CacheFolder, $cacheFileName, $cacheName)) {
                $cacheUrl = $s3CacheFolderURL . $cacheFileName;
                unlink($cacheName);
            }
            if($this->uploadFile("", $s3CacheFolder, $cacheFileName2, $cacheName2)) {
                $cacheUrl2 = $s3CacheFolderURL . $cacheFileName2;
                unlink($cacheName2);
            }
        }
        array_push($cacheUrls, $cacheUrl);
        array_push($cacheUrls, $cacheUrl2);
        return $cacheUrls;
    }

    public function uploadFile($parentContainer, $container, $fileName, $file) {
        $newPath = $parentContainer.rtrim($container,'/').'/';
        if (!$this->s3->driver->if_object_exists($this->s3->bucket, $newPath)) {
            $this->s3->createContainer($container, $parentContainer);
        }
        $newPath = $parentContainer.rtrim($container,'/').'/' . $fileName;
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $size = @filesize($file);
        $uploaded = $this->s3->driver->create_object(
            $this->s3->bucket,
            $newPath,
            array(
            'fileUpload' => $file,
            'acl' => AmazonS3::ACL_PUBLIC,
            'length' => $size,
            'contentType' => $this->s3->getContentType($extension),
        ));

        if (!$uploaded) {
            return false;
        }

        return true;
    }
}

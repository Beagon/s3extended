<?php
//////////////////////////////////////////////////////////////
///  S3 Extended by Robin Rijkeboer <robin@qaraqter.nl>     //
//   available at https://github.com/Qaraqter/s3extended   ///
//////////////////////////////////////////////////////////////
///                                                         //
//       properties.inc.php - properties lexicon            //
//                                                         ///
//////////////////////////////////////////////////////////////
$_lang['s3extended.url_desc'] = 'The URL of the Amazon S3 instance.';
$_lang['s3extended.bucket_desc'] = 'The S3 Bucket to load your data from.';
$_lang['s3extended.key_desc'] = 'The Amazon key for authentication to the bucket.';
$_lang['s3extended.secret_key_desc'] = 'The Amazon secret key for authentication to the bucket.';
$_lang['s3extended.imageExtensions_desc'] = 'A comma-seperated list of file extensions to use as images. S3 Extended will attempt to make thumbnails of files with these extensions.';
$_lang['s3extended.thumbnailType_desc'] = 'The image type to render thumbnails as.';
$_lang['s3extended.thumbnailQuality_desc'] = 'The quality of the rendered thumbnails, in a scale from 0-100.';
$_lang['s3extended.skipFiles_desc'] = 'A comma-seperated list. S3 Extended will skip over and hide files and folders that match any of these.';
$_lang['s3extended.downSize_desc'] = 'A yes or no check. If yes S3 Extended will downsize images before upload.';
$_lang['s3extended.downSizeWidth_desc'] = 'The width that will be used for the downsizing of images when downSize is set on Yes.';
$_lang['s3extended.downSizeHeight_desc'] = 'The height that will be used for the downsizing of images when downSize is set on Yes.';
$_lang['s3extended.downSizeQuality_desc'] = 'The quality percentage of the downsized image, in a scale from 0-100 when downSize is set on Yes.';
$_lang['s3extended.sanitizeFiles_desc'] = 'A yes or no check. If yes your file name will be sanitized on upload. eg. "My Vacation Photo 1.PNG" will be called "my-vacation-photo-1.png"';
$_lang['s3extended.keepRatio_desc'] = 'A yes or no check. If yes the aspect ratio of your image will be preserved and the image is scaled on width.';
$_lang['s3extended.s3CacheFolder_desc'] = 'To be implemented.';
$_lang['s3extended.hideS3Cache_desc'] = 'To be implemented.';
$_lang['s3extended.cacheToS3_desc'] = 'A yes or no check. If yes S3 will be used for your cache.';

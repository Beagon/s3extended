<?php
$events = array('OnFileManagerUpload');
$eventName = $modx->event->name;

function checkDir($dir, $source) {
    $files = array();
    $imageExtensions = $source->getOption('imageExtensions', $source->properties, 'jpg,jpeg,png,gif');
    $imageExtensions = explode(',', $imageExtensions);
    $itemList = $source->getContainerList($dir); //Get all object in directory
    foreach ($itemList as $object) {
        if ($object['type'] == 'dir') { //Check if the object is a DIR
            $files = array_merge($files, checkDir($object['pathRelative'], $source)); //Merges the files array with the result of this function (Recursive function)
        }
        
        $ext = strtolower(pathinfo($object['text'], PATHINFO_EXTENSION)); //Get's the file extension in lowercase

        if (in_array($ext, $imageExtensions) && $object['type'] == "file") //Is the ext in the imageExtensions array and is the object a file
        {
            if ($source->getOption('debugMode', $source->properties, 'No')  == "Yes") error_log("Found " . $object['pathRelative'] . " adding to array.");
            array_push($files, $object['url']); //Pushes file url to Files array
        }
    }    
     
    return $files; //Return the files array which should be filled with S3 urls
}

if (in_array($eventName, $events)) { //Is the event in the $events array
    if ($source->driver->if_object_exists($source->bucket, ".cached")) {
    return "";    
    }
    if (method_exists($source, 'getCodeName') && $source->getCodeName() == "s3e") { //Checks if $source has the function and if so does it return s3e
        if ($source->getOption('debugMode', $source->properties, 'No') == "Yes") error_log("Getting all the objects!");
        $files = checkDir("", $source); //Get all the objects
        foreach ($files as $file) {
           $source->functions->generateFileManagerThumbs($file); //Generate thumb files for array.
        }
        $source->createObject("", ".cached", "s3e");
    }
}
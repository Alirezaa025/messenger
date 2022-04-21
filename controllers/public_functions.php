<?php

/**
 * number_format(number, count of number after decimal)
 *
 * @param integer $bytes
 * @return string $size
 */
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $size = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $size = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $size = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $size = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $size = $bytes . ' byte';
    } else {
        $size = '0 bytes';
    }

    return $size;
}

/**
 * because rmdir method just remove empty directory, in this function: 
 * if find file delete that
 * if find directory execute function for that directory
 */

function deleteDirectory($dir)
{

    if (!file_exists($dir)) {   //return if chosen file does not exist
        return true;
    }

    if (!is_dir($dir)) {    // delete files
        return unlink($dir);
    }

    $scan = scanDirectory($dir);

    foreach ($scan as $item) {
        if (!deleteDirectory($dir . '/' . $item)) { // if all files are deleted return true
            return false;
        }
    }

    return rmdir($dir);
}

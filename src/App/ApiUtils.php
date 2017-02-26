<?php

namespace App;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApiUtils
{
    /**
     * Check that file is really file
     *
     * @param $file
     * @return bool
     */
    public static function checkRequestFile($file)
    {
        if (!$file) {
            return false;
        }
        if ($file instanceof UploadedFile) {
            return true;
        }
        return false;
    }


    /**
     * Check that id is really id
     *
     * @param integer $id
     * @return bool|integer
     */
    public static function checkRequestId($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT)) {
            return intval($id);
        }
        return false;
    }


    /**
     * Check that string not empty
     *
     * @param string $requestString
     * @return bool
     */
    public static function checkRequestString($requestString)
    {
        if (!is_string($requestString)
            || strlen($requestString) == 0
        ) {
            return false;
        }
        return true;
    }
}

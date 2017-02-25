<?php

namespace App;

class ApiUtils
{
    /**
     * @param $file
     * @return bool
     */
    public static function checkRequestFile($file)
    {
        if (!$file) {
            return false;
        }
        return true;
    }


    /**
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
     * @param string $requestString
     * @return bool
     */
    public static function checkRequestString($requestString)
    {
        if (!is_string($requestString) || strlen($requestString) == 0) {
            return false;
        }
        return true;
    }
}

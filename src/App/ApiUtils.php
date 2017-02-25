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
        $idParseInt = intval($id);
        if (!is_int($idParseInt)) {
            return false;
        }
        if ($idParseInt == 0) {
            return false;
        }
        return $idParseInt;
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

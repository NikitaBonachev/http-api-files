<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ApiUtils
 * @package App
 */
class ApiUtils
{
    /**
     * Check that file is exist in request
     *
     * @param Request $request
     * @return UploadedFile|false
     */
    public static function checkRequestFile(Request $request)
    {
        $file = null;
        try {
            $file = $request->files->get('upload_file');
        } catch (\Exception $e) {

        }

        if (!$file) {
            $file = false;
        }

        return $file;
    }


    /**
     * Check that id is really id (not very useful method)
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


    /**
     * Return the request content string
     *
     * @param $requestString
     * @return string
     */
    public static function checkRequestLength($requestString)
    {
        if (strlen($requestString) < 500) {
            return $requestString;
        } else {
            return 'Sorry, but the request content is too long';
        }
    }
}

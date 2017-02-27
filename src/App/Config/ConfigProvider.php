<?php

namespace App\Config;

/**
 * Class ConfigProvider
 * @package App\Config
 */
class ConfigProvider
{
    /**
     * Return path of config file
     *
     * @return string
     */
    private static function defaultConfigPath()
    {
        return __DIR__ . '/../../../config.php';
    }


    /**
     * Setting configuration
     *
     * @param string $name
     * @param string $filePath - custom configuration file
     *
     * @return array
     * @throws \Exception
     */
    private static function file($name, $filePath = null)
    {
        if (!$filePath) {
            $filePath = self::defaultConfigPath();
        }

        if (is_file($filePath)) {
            $arr = require $filePath;
            if (isset($arr[$name])) {
                return $arr[$name];
            } else {
                throw new \Exception('Settings empty. Name: ' . $name);
            }
        } else {
            throw new \Exception('Settings empty. Dir: ' . $filePath);
        }
    }


    /**
     * Return database config
     *
     * @param $env - environment param
     *
     * @return array|\Exception
     * @throws \Exception
     */
    public static function getDatabaseConfig($env)
    {
        $databases = self::file('databases');

        if (is_array($databases) && isset($databases[$env])) {
            return $databases[$env];
        } else {
            throw new \Exception('Settings database empty. Environment: ' . $env);
        }
    }


    /**
     * Return path upload folder
     *
     * @param $env
     *
     * @return array|\Exception
     * @throws \Exception
     */
    public static function getUploadDir($env)
    {
        $uploadDirs = self::file('uploadDirs');

        if (is_array($uploadDirs) && isset($uploadDirs[$env])) {
            return $uploadDirs[$env];
        } else {
            throw new \Exception('Settings upload dir empty. Environment: ' . $env);
        }
    }


    /**
     * Return path upload folder
     *
     * @param $env
     *
     * @return array|\Exception
     * @throws \Exception
     */
    public static function getLogFile($env)
    {
        $uploadDirs = self::file('log');

        if (is_array($uploadDirs) && isset($uploadDirs[$env])) {
            return $uploadDirs[$env];
        } else {
            throw new \Exception('Settings log empty. Environment: ' . $env);
        }
    }
}

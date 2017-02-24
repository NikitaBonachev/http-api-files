<?php

namespace App\Config;

class ConfigProvider
{
    static private function defaultConfigPath()
    {
        return __DIR__ . '/../../../config.php';
    }


    /**
     * Setting configuration
     *
     * @param string $name
     * @param string $filePath
     *
     * @return array
     * @throws \Exception
     */
    static private function file($name, $filePath = null)
    {
        if (!$filePath) {
            $filePath = self::defaultConfigPath();
        }

        if (is_file($filePath)) {
            $arr = require $filePath;
            if (isset($arr[$name])) {
                return $arr[$name];
            } else {
                throw new \Exception('Settings empty . Name: ' . $name);
            }
        } else {
            throw new \Exception('Settings empty . Dir: ' . $filePath);
        }

    }


    /**
     * @param $env
     *
     * @return array|\Exception
     * @throws \Exception
     */
    static public function getDatabaseConfig($env)
    {
        $databases = self::file('databases');

        if (is_array($databases) && isset($databases[$env])) {
            return $databases[$env];
        } else {
            throw new \Exception('Settings database empty . Env: ' . $env);
        }
    }


    /**
     * @param $env
     *
     * @return array|\Exception
     * @throws \Exception
     */
    static public function getUploadDir($env)
    {
        $uploadDirs = self::file('uploadDirs');

        if (is_array($uploadDirs) && isset($uploadDirs[$env])) {
            return $uploadDirs[$env];
        } else {
            throw new \Exception('Settings upload dir empty . Env: ' . $env);
        }
    }

}

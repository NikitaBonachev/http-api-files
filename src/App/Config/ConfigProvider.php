<?php

namespace App\Config;

class ConfigProvider
{

    static private function defaultConfigPath()
    {
        return __DIR__ . '/../../../dev_config.php';
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
     * Database
     *
     * @return array
     */
    static public function getDB()
    {
        return self::file('database');
    }
}

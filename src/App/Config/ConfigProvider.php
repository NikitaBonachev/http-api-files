<?php

namespace App\Config;

class ConfigProvider
{
    /**
     * Setting configuration
     *
     * @param string $name
     * @return array
     * @throws \Exception
     */
    static private function file($name){
        $filePath = __DIR__ . '/../../../dev_config.php';

        if (is_file($filePath)) {
            $arr = require $filePath;
            if (is_array($arr)) {
                return $arr[$name];
            }
        }

        throw new \Exception('Settings empty . Dir: ' . $filePath);
    }

    /**
     * Database
     *
     * @return array
     */
    static public function getDB() {
        return self::file('database');
    }
}

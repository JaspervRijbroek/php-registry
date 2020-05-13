<?php

declare(strict_types=1);

namespace Library;

class Config
{
    private static $config = [];

    public static function get(string $item)
    {
        // First part is the file, following parts is the item.
        $parts = explode('.', $item);
        $filename = array_shift($parts);

        if(!isset(self::$config[$filename])) {
            $file = Autoloader::getRoot() . '/config/' . $filename . '.php';

            if (file_exists($file)) {
                self::$config[$filename] = require_once $file;
            }
        }

        return array_reduce(
            $parts,
            function ($carry, $key) {
                if (!$carry || !isset($carry[$key])) {
                    return false;
                }

                return $carry[$key];
            },
            self::$config[$filename]
        );
    }
}
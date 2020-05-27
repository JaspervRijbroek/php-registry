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

        return array_reduce(
            $parts,
            function ($carry, $key) {
                if (!$carry || !isset($carry[$key])) {
                    return false;
                }

                return $carry[$key];
            },
            self::getConfig()
        );
    }

    private static function getConfig(): array
    {
        if(empty(self::$config)) {
            self::$config = require_once getcwd() . '/config.php';
        }

        return self::$config;
    }
}
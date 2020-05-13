<?php

declare(strict_types=1);

namespace Library;

class Autoloader
{
    private static $_isRegistered = false;
    private static $_namespaces = [
        'App\\' => 'app/',
        'Library\\' => 'lib/'
    ];

    public static function register()
    {
        if (!self::$_isRegistered) {
            // Register the autoloader
            spl_autoload_register([Autoloader::class, 'load']);
        }
    }

    public static function load(string $class)
    {
        // We will now load the classes based upon psr-4
        // If a prefix is found, we will use it else we will check it as usual.
        foreach (self::$_namespaces as $namespace => $path) {
            if (strpos($class, $namespace) === 0) {
                $requirePath = str_replace($namespace, $path, $class);
                $requirePath = str_replace('\\', DIRECTORY_SEPARATOR, $requirePath);
                $requirePath = self::getRoot() . DIRECTORY_SEPARATOR . $requirePath . '.php';

                if (file_exists($requirePath)) {
                    require_once $requirePath;
                }
            }
        }
    }

    public static function getRoot()
    {
        return dirname(__DIR__);
    }

    public static function getClassesInDir($folder, $pattern)
    {
        $folder = self::getRoot() . $folder;
        $dir = new \RecursiveDirectoryIterator($folder);
        $ite = new \RecursiveIteratorIterator($dir);
        $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
        $fileList = [];

        foreach ($files as $file) {
            $fileList = array_merge($fileList, $file);
        }

        return array_map(
            function ($file) use ($folder) {
                $filename = str_replace([$folder, '.php'], '', $file);

                return str_replace(DIRECTORY_SEPARATOR, '\\', $filename);
            },
            $fileList
        );
    }
}
<?php

declare(strict_types=1);

namespace Library;

class Application
{
    public static function run()
    {
        self::registerController();

        Router::run();
    }

    /**
     * This method will load in all the controllers and will register their routes.
     * This is to learn about how annotations are being parsed.
     */
    private static function registerController(): void
    {
        $controllers = Autoloader::getClassesInDir('/app/Controller/', "/.*php/");
        $namespace = 'App\\Controller\\';

        foreach ($controllers as $controller) {
            Annotations::getInstance()->parseMethodAnnotations($namespace . $controller);
        }

        return;
    }
}
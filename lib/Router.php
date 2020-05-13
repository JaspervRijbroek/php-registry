<?php

declare(strict_types=1);

namespace Library;

class Router
{

    private static $routes = array();
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;

    public static function register()
    {
        Annotations::getInstance()->registerAnnotation(
            'route',
            function (string $class, string $method, array $args): void {
                self::add($args[0], [$class, $method], $args[1] ?? 'get');
            }
        );
    }

    public static function add($expression, $function, $method = 'get')
    {
        array_push(
            self::$routes,
            array(
                'expression' => $expression,
                'function' => $function,
                'method' => $method
            )
        );
    }

    public static function pathNotFound($function)
    {
        self::$pathNotFound = $function;
    }

    public static function methodNotAllowed($function)
    {
        self::$methodNotAllowed = $function;
    }

    public static function run($basepath = '/')
    {
        // Parse current url
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);//Parse Uri

        if (isset($parsed_url['path'])) {
            $path = $parsed_url['path'];
        } else {
            $path = '/';
        }

        // Get current request method
        $method = $_SERVER['REQUEST_METHOD'];

        $path_match_found = false;

        $route_match_found = false;

        foreach (self::$routes as $route) {
            // If the method matches check the path

            // Add basepath to matching string
            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }

            // Add 'find string start' automatically
            $route['expression'] = '^' . $route['expression'];

            // Add 'find string end' automatically
            $route['expression'] = $route['expression'] . '$';

            // Check path match
            if (preg_match('#' . $route['expression'] . '#', $path, $matches)) {
                $path_match_found = true;

                // Check method match
                if (strtolower($method) == strtolower($route['method'])) {
                    array_shift($matches);// Always remove first element. This contains the whole string

                    if ($basepath != '' && $basepath != '/') {
                        array_shift($matches);// Remove basepath
                    }

                    self::dispatch($route, $matches);

                    $route_match_found = true;

                    // Do not check other routes
                    break;
                }
            }
        }

        // No matching route was found
        if (!$route_match_found) {
            // But a matching path exists
            if ($path_match_found) {
                self::sendResponse(405, 'Method Not Allowed');
            } else {
                self::sendResponse(404, 'Not found');
            }
        }
    }

    static private function dispatch(array $route, array $params): void
    {
        $request = new Request();
        $response = new Response();

        $request->setParams($params);

        $controller = $route['function'][0] = new $route['function'][0]($request, $response);

        try {
            echo call_user_func_array($route['function'], [$request, $response]);
        } catch (\Exception $e) {
            self::sendResponse(500, $e->getMessage());
        } catch (\Error $e) {
            self::sendResponse(500, $e->getMessage());
        }

        return;
    }

    static private function sendResponse(int $status, string $message): void
    {
        $response = new Response();
        echo $response
            ->setStatus($status)
            ->setBody(
                [
                    'message' => $message
                ]
            );

        return;
    }
}
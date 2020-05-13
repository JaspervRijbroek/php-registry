<?php

declare(strict_types=1);

namespace Library;

class Annotations
{
    private static $instance = false;

    private $annotations = [];

    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new Annotations();
        }

        return self::$instance;
    }

    public function registerAnnotation($method, callable $callable)
    {
        $this->annotations[$method] = $callable;
    }

    public function parseMethodAnnotations($class)
    {
        $reflection = new \ReflectionClass($class);

        // Check out the public methods.
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $comment = $method->getDocComment();

            if (!$comment) {
                continue;
            }

            // parse the docblock
            $annotations = $this->parseDocBlock($comment);

            // Check if the annotation is registered, if so execute it.
            foreach($annotations as $annotation) {
                if(isset($this->annotations[$annotation[0]])) {
                    $this->annotations[$annotation[0]]($method->class, $method->name, $annotation[1]);
                }
            }
        }
    }

    private function parseDocBlock($docblock): array
    {
        // We will keep it simple, for every line, we will check for an @, if there is an @, I will parse this line.
        $lines = explode(PHP_EOL, $docblock);
        $lines = array_map('trim', $lines);

        $annotations = array_filter($lines, function($line) {
            return strpos($line, '@');
        });

        return array_map(function($annotationLine) {
            if(preg_match('/@(.*?)\((.*?)\)$/', $annotationLine, $matches)) {
                return [strtolower($matches[1]), str_getcsv($matches[2])];
            }
        }, $annotations);
    }
}
<?php

declare(strict_types=1);

namespace Library;

class Request
{
    private $params = [];
    private $body = false;
    private $headers = false;

    // We don't need to filter data as Medoo does this already, but just to have a fancy wrapper.

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params = []): Request
    {
        $this->params = $params;

        return $this;
    }

    public function getBody(string $param = '')
    {
        $body = file_get_contents('php://input');

        if($body && !$this->body) {
            // We only have JSON.
            $this->body = json_decode($body, true);
        }

        if(empty($param)) {
            return $this->body;
        }

        return array_reduce(explode('.', $param), function($carry, $key) {
            if(!$carry || !isset($carry[$key])) {
                return false;
            }

            return $carry[$key];
        }, $this->body);
    }

    public function getHeader(string $header = '')
    {
        if(!$this->headers)
        {
            $this->headers = getallheaders();
        }

        return $this->headers[$header] ?? false;
    }
}
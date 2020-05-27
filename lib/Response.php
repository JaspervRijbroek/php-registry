<?php

declare(strict_types=1);

namespace Library;

class Response
{
    public const STATUS_CREATED = 201;
    public const STATUS_OK = 200;
    private $status = 200;
    private $body = [];

    public function setStatus(int $status = self::STATUS_OK): Response
    {
        $this->status = $status;

        return $this;
    }

    public function downloadFile(string $filePath): void
    {
        http_response_code($this->status);
        header('Content-Type: application/octet-stream');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');

        readfile($filePath);

        return;
    }

    public function __toString(): string
    {
        http_response_code($this->status);
        header('Content-Type: application/json');

        return $this->getBody();
    }

    public function getBody(): string
    {
        return json_encode($this->body);
    }

    public function setBody(array $body): Response
    {
        $this->body = $body;

        return $this;
    }
}
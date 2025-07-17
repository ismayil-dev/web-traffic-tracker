<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Http;

class Response
{
    private array $data;
    private int $statusCode;

    public function __construct(array $data, int $statusCode)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public static function success(array $payload = []): self
    {
        return new self($payload, 200);
    }

    public static function created(array $payload = []): self
    {
        return new self($payload, 201);
    }

    public static function empty(): self
    {
        return new self([], 204);
    }

    public static function error(string $message, int $statusCode = 400): self
    {
        return new self(static::buildError($statusCode, [$message]), $statusCode);
    }

    public static function unProcessableContent(array $validationErrors): self
    {
        return new self(static::buildError(422, $validationErrors), 422);
    }

    public static function notFound(string $message = 'Not Found'): self
    {
        return new self(static::buildError(404, [$message]), 404);
    }

    private static function buildError(
        int $statusCode,
        array $messages,
    ): array {
        return [
            'statusCode' => $statusCode,
            'messages' => $messages,
        ];
    }

    public function toJson(array $append = []): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');

        if ($this->statusCode !== 204) {
            echo json_encode(array_merge($this->data, $append));
        }

        exit;
    }
}

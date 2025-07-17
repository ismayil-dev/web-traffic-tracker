<?php

declare(strict_types=1);

namespace TrafficTracker\Infrastructure\Http;

use TrafficTracker\Infrastructure\Exception\MethodNotAllowed;
use TrafficTracker\Infrastructure\Logging\AppLogger;
use TrafficTracker\Presentation\Middleware\Cors;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__.'/../../Presentation/routes.php';
    }

    /**
     * @throws MethodNotAllowed
     */
    public function load(string $uri, ?string $method = 'GET'): void
    {
        // Handle CORS for all requests
        new Cors()->handle();

        $parsedUrl = parse_url($uri);
        $baseUrl = $parsedUrl['path'];

        if (!isset($this->routes[$baseUrl])) {
            AppLogger::instance()->error($baseUrl.' not found');
            Response::notFound('Route not found'.$baseUrl)->toJson();
        }

        $route = $this->routes[$baseUrl];

        if ($route['method'] !== $method) {
            throw new MethodNotAllowed($baseUrl);
        }


        $middlewares = $route['middleware'] ?? [];

        foreach ($middlewares as $middleware) {
            $middlewareInstance = new $middleware();
            $middlewareInstance->handle();
        }

        $controller = new $route['handler']();
        $result = $controller->__invoke();

        if ($result instanceof Response) {
            $result->toJson();
        }

        if (is_array($result)) {
            $response = new Response($result, $result['statusCode'] ?? 200);
            $response->toJson();
        }

    }
}

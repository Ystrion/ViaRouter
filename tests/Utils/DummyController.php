<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Tests\Utils;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ystrion\ViaRouter\Attributes\Route;

class DummyController
{
    #[Route('root', '/')]
    public static function root(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    #[Route('static-route', '/users')]
    public static function staticRoute(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    /**
     * @param array<string, string> $args
     */
    #[Route(
        'dynamic-route',
        '/users/{id}/{username}',
        defaults: ['username' => 'IDK'],
        constraints: ['id' => '[0-9]+']
    )]
    public static function dynamicRoute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        return $response;
    }
}

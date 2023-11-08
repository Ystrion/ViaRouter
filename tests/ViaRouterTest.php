<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Tests;

use DI\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ystrion\HttpExceptions\Client\NotFound;
use Ystrion\MiddlewareDispatcher\QueueMiddlewareDispatcher;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Routes;
use Ystrion\ViaRouter\Tests\Utils\DummyController;
use Ystrion\ViaRouter\ViaRouter;

#[CoversClass(ViaRouter::class)]
final class ViaRouterTest extends TestCase
{
    public function testClassicMatch(): void
    {
        $routes = new Routes();

        $routes->add('root', '/', fn() => 'dummy');
        $routes->add('static-route', '/users', fn() => 'dummy');
        $routes->add('dynamic-route', '/users/{id}/{username}', fn() => 'dummy')
               ->defaults(['username' => 'IDK'])
               ->constraints(['id' => '\d+']);

        $router = new ViaRouter($routes);

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('root', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('static-route', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('IDK', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12/Ystrion');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('Ystrion', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/NaN/Ystrion');
        self::assertFalse($router->match($request));
    }

    public function testControllerMatch(): void
    {
        $routes = new Routes();

        $routes->addController(DummyController::class);

        $router = new ViaRouter($routes);

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('root', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('static-route', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('IDK', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12/Ystrion');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('Ystrion', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/NaN/Ystrion');
        self::assertFalse($router->match($request));
    }

    public function testControllerMatchWithContainer(): void
    {
        $routes = new Routes();

        $routes->addController(DummyController::class);

        $router = new ViaRouter($routes, container: new Container());

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('root', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('static-route', $router->match($request)->getName());

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('IDK', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/12/Ystrion');
        self::assertInstanceOf(Route::class, $router->match($request));
        self::assertSame('dynamic-route', $router->match($request)->getName());
        self::assertSame('Ystrion', $router->match($request)->getParam('username'));

        $request = (new Psr17Factory())->createServerRequest('GET', '/users/NaN/Ystrion');
        self::assertFalse($router->match($request));
    }

    public function testClassicGenerate(): void
    {
        $routes = new Routes();

        $routes->add('root', '/', fn() => 'dummy');
        $routes->add('static-route', '/users', fn() => 'dummy');
        $routes->add('dynamic-route', '/users/{id}/{username}', fn() => 'dummy')
               ->defaults(['username' => 'IDK'])
               ->constraints(['id' => '\d+']);

        $router = new ViaRouter($routes);

        self::assertSame('/', $router->generate('root'));
        self::assertSame('/users', $router->generate('static-route'));
        self::assertSame('/users/10/Ystrion', $router->generate('dynamic-route', [
            'id' => '10',
            'username' => 'Ystrion'
        ]));
    }

    public function testControllerGenerate(): void
    {
        $routes = new Routes();

        $routes->addController(DummyController::class);

        $router = new ViaRouter($routes);

        self::assertSame('/', $router->generate('root'));
        self::assertSame('/users', $router->generate('static-route'));
        self::assertSame('/users/10/Ystrion', $router->generate('dynamic-route', [
            'id' => '10',
            'username' => 'Ystrion'
        ]));
    }

    public function testMiddlewareWithRouteFound(): void
    {
        $routes = new Routes();

        $routes->add('dynamic-route', '/users/{id}/{username}', function ($request, $response, $args) {
            return 'Hello ' . $args['username'] . '!';
        });

        $psr17Factory = new Psr17Factory();
        $request = $psr17Factory->createServerRequest('GET', '/users/10/Ystrion');

        $middlewareDispatcher = new QueueMiddlewareDispatcher($psr17Factory);

        $middlewareDispatcher->add([
            new ViaRouter($routes)
        ]);

        $response = $middlewareDispatcher->handle($request);

        self::assertSame('Hello Ystrion!', (string) $response->getBody());
    }

    public function testMiddlewareWithRouteNotFound(): void
    {
        self::expectException(NotFound::class);

        $psr17Factory = new Psr17Factory();
        $request = $psr17Factory->createServerRequest('GET', '/');

        $middlewareDispatcher = new QueueMiddlewareDispatcher($psr17Factory);

        $middlewareDispatcher->add([
            new ViaRouter(new Routes())
        ]);

        $middlewareDispatcher->handle($request);
    }
}

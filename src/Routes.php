<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter;

use ArrayIterator;
use IteratorAggregate;
use ReflectionClass;
use ReflectionMethod;
use Ystrion\ViaRouter\Attributes\Route as RouteAttribute;

/**
 * @implements IteratorAggregate<string, Route>
 */
class Routes implements IteratorAggregate
{
    /** @var array<string, Route> */
    protected array $routes = [];

    public function get(string $name): ?Route
    {
        return $this->routes[$name] ?? null;
    }

    public function add(string $name, string $path, callable $handler): Route
    {
        return $this->routes[$name] = new Route($name, $path, $handler);
    }

    /**
     * @param object|class-string $controller
     */
    public function addController(object|string $controller): void
    {
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $routeAttributes = $method->getAttributes(RouteAttribute::class);

            foreach ($routeAttributes as $routeAttribute) {
                $route = $routeAttribute->newInstance();

                $this->routes[$route->name] = (new Route($route->name, $route->path, [$controller, $method->getName()]))
                    ->methods($route->methods)
                    ->defaults($route->defaults)
                    ->constraints($route->constraints)
                    ->host($route->host);
            }
        }
    }

    public function compile(): void
    {
        foreach ($this->routes as $name => $route) {
            $route->setRegex(RouteParser::parse($route));
        }
    }

    /**
     * @return ArrayIterator<string, Route>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->routes);
    }
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter;

use DI\Container;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ystrion\HttpExceptions\Client\NotFound;
use Ystrion\ViaRouter\Rules\HostRule;
use Ystrion\ViaRouter\Rules\MethodsRule;
use Ystrion\ViaRouter\Rules\PathRule;
use Ystrion\ViaRouter\Rules\RuleInterface;

class ViaRouter implements MiddlewareInterface
{
    /** @var RuleInterface[] */
    protected array $rules = [];

    /**
     * @param RuleInterface[] $additionalRules
     */
    public function __construct(
        protected Routes $routes,
        array $additionalRules = [],
        protected Container $container = new Container()
    ) {
        $this->rules = array_merge([
            new MethodsRule(),
            new HostRule(),
            new PathRule()
        ], $additionalRules);
    }

    public function match(ServerRequestInterface $request): Route|false
    {
        foreach ($this->routes as $name => $route) {
            $route = clone $route;

            foreach ($this->rules as $rule) {
                if (!$rule->match($request, $route)) {
                    continue 2;
                }
            }

            return $route;
        }

        return false;
    }

    /**
     * @param array<string, string> $params
     */
    public function generate(string $name, array $params = []): string
    {
        $route = $this->routes->get($name);

        if ($route === null) {
            throw new NotFound();
        }

        $path = $route->getPath();

        foreach ($params as $param => $value) {
            $path = str_replace('{' . $param . '}', $value, $path);
        }

        return $path;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->match($request);

        if ($route === false) {
            throw new NotFound();
        }

        $response = $handler->handle($request);

        $handlerResponse = $this->container->call($route->getHandler(), [
            'request' => $request,
            'response' => $response,
            'args' => $route->getParams()
        ]);

        if (is_string($handlerResponse)) {
            $response->getBody()->write($handlerResponse);

            $handlerResponse = $response;
        }

        if (!$handlerResponse instanceof ResponseInterface) {
            throw new InvalidArgumentException('Your handler must return an instance of ResponseInterface.');
        }

        return $handlerResponse;
    }
}

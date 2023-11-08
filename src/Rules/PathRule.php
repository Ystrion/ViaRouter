<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Rules;

use Psr\Http\Message\ServerRequestInterface;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\RouteParser;
use Ystrion\ViaRouter\Rules\RuleInterface;

class PathRule implements RuleInterface
{
    public function match(ServerRequestInterface $request, Route $route): bool
    {
        $regex = $route->getRegex() ?? RouteParser::parse($route);

        preg_match($regex, ltrim($request->getUri()->getPath(), '/'), $matches);

        if (count($matches) === 0) {
            return false;
        }

        $params = $route->getParams();

        foreach ($matches as $name => $value) {
            if (array_key_exists($name, $params)) {
                $params[$name] = rawurldecode($value);
            }
        }

        $route->defaults($params);

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter;

class RouteParser
{
    protected const REGEX = '#/?{(?P<name>[a-zA-Z_][a-zA-Z0-9_-]*)}#';

    public static function parse(Route $route): string
    {
        $regex = ltrim($route->getPath(), '/');
        $params = $route->getParams();

        preg_match_all(self::REGEX, $regex, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $subpattern = self::getSubpattern($route, str_starts_with($match[0], '/'), $match['name']);
            $regex = str_replace(ltrim($match[0], '/'), $subpattern, $regex);

            if (!array_key_exists($match['name'], $params)) {
                $params[$match['name']] = null;
            }
        }

        $route->defaults($params);

        return "#^{$regex}$#";
    }

    protected static function getSubpattern(Route $route, bool $startsWithSlash, string $name): string
    {
        $constraint = $route->getConstraint($name) ?? '[^/]+';

        if ($route->hasParam($name)) {
            if ($startsWithSlash) {
                return "?(?P<{$name}>{$constraint})?";
            }

            return "(?P<{$name}>{$constraint})?";
        }

        return "(?P<{$name}>{$constraint})";
    }
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Rules;

use Psr\Http\Message\ServerRequestInterface;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Rules\RuleInterface;

class HostRule implements RuleInterface
{
    public function match(ServerRequestInterface $request, Route $route): bool
    {
        return $route->getHost() === null || $request->getUri()->getHost() === $route->getHost();
    }
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Rules;

use Psr\Http\Message\ServerRequestInterface;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Rules\RuleInterface;

class MethodsRule implements RuleInterface
{
    public function match(ServerRequestInterface $request, Route $route): bool
    {
        return in_array($request->getMethod(), $route->getMethods(), true);
    }
}

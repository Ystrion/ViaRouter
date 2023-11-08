<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Rules;

use Psr\Http\Message\ServerRequestInterface;
use Ystrion\ViaRouter\Route;

interface RuleInterface
{
    public function match(ServerRequestInterface $request, Route $route): bool;
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Tests\Rules;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Rules\MethodsRule;

#[CoversClass(MethodsRule::class)]
final class MethodsRuleTest extends TestCase
{
    public function testMatch(): void
    {
        $rule = new MethodsRule();
        $route = (new Route('root', '/', fn() => 'dummy'))->methods(['GET', 'PUT']);

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertTrue($rule->match($request, $route));

        $request = (new Psr17Factory())->createServerRequest('PUT', '/');
        self::assertTrue($rule->match($request, $route));

        $request = (new Psr17Factory())->createServerRequest('POST', '/');
        self::assertFalse($rule->match($request, $route));
    }
}

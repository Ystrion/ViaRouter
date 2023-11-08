<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Tests\Rules;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Rules\HostRule;

#[CoversClass(HostRule::class)]
final class HostRuleTest extends TestCase
{
    public function testMatch(): void
    {
        $rule = new HostRule();
        $route = (new Route('root', '/', fn() => 'dummy'))->host('foo.example.com');

        $request = (new Psr17Factory())->createServerRequest('GET', (new Uri('/'))->withHost('foo.example.com'));
        self::assertTrue($rule->match($request, $route));

        $request = (new Psr17Factory())->createServerRequest('GET', (new Uri('/'))->withHost('example.com'));
        self::assertFalse($rule->match($request, $route));
    }
}

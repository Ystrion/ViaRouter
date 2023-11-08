<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Tests\Rules;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ystrion\ViaRouter\Route;
use Ystrion\ViaRouter\Rules\PathRule;

#[CoversClass(PathRule::class)]
final class PathRuleTest extends TestCase
{
    public function testVoidRoute(): void
    {
        $rule = new PathRule();
        $route = new Route('void', '', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertTrue($rule->match($request, clone $route));
    }

    public function testRootRoute(): void
    {
        $rule = new PathRule();
        $route = new Route('root', '/', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/');
        self::assertTrue($rule->match($request, clone $route));
    }

    public function testStaticRoute(): void
    {
        $rule = new PathRule();
        $route = new Route('static-route', '/foo/bar', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo');
        self::assertFalse($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar/baz');
        self::assertFalse($rule->match($request, clone $route));
    }

    public function testRouteWithTrailingSlash(): void
    {
        $rule = new PathRule();
        $route = new Route('without-trailing-slash', '/foo/bar', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar/');
        self::assertFalse($rule->match($request, clone $route));

        $route = new Route('with-trailing-slash', '/foo/bar/', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar/');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/foo/bar');
        self::assertFalse($rule->match($request, clone $route));
    }

    public function testRouteWithPlaceholder(): void
    {
        $rule = new PathRule();
        $route = new Route('placeholder', '/placeholder/{placeholder}', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholder/foo');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholder');
        self::assertFalse($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholder/foo/bar');
        self::assertFalse($rule->match($request, clone $route));
    }

    public function testRouteWithPlaceholders(): void
    {
        $rule = new PathRule();
        $route = new Route('placeholders', '/placeholders/{placeholder1}/{placeholder2}', fn() => 'dummy');

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholders/foo/bar');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholder');
        self::assertFalse($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholder/foo');
        self::assertFalse($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/placeholders/foo/bar/baz');
        self::assertFalse($rule->match($request, clone $route));
    }

    public function testRouteWithDefaults(): void
    {
        $rule = new PathRule();
        $route = (new Route('defaults', '/defaults/{placeholder1}/{placeholder2}', fn() => 'dummy'))
            ->defaults(['placeholder1' => 'foo', 'placeholder2' => 'bar']);

        $request = (new Psr17Factory())->createServerRequest('GET', '/defaults');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/defaults/foo');
        self::assertTrue($rule->match($request, clone $route));
    }

    public function testRouteWithConstraints(): void
    {
        $rule = new PathRule();
        $route = (new Route('constraints', '/constraints/{placeholder1}/{placeholder2}', fn() => 'dummy'))
            ->constraints(['placeholder1' => '[a-z]+', 'placeholder2' => '[0-9]+']);

        $request = (new Psr17Factory())->createServerRequest('GET', '/constraints/foo/01');
        self::assertTrue($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/constraints/foo/bar');
        self::assertFalse($rule->match($request, clone $route));

        $request = (new Psr17Factory())->createServerRequest('GET', '/constraints/01/bar');
        self::assertFalse($rule->match($request, clone $route));
    }
}

<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter;

class Route
{
    protected ?string $regex = null;

    /** @var string[] */
    protected array $methods = ['GET'];

    /** @var array<string, string|null> */
    protected array $params = [];

    /** @var array<string, string> */
    protected array $constraints = [];

    protected ?string $host = null;

    /**
     * @param callable|array{object|class-string, non-empty-string}|class-string<object&callable> $handler
     */
    public function __construct(
        protected string $name,
        protected string $path,
        protected $handler
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRegex(): ?string
    {
        return $this->regex;
    }

    public function setRegex(?string $regex): void
    {
        $this->regex = $regex;
    }

    /**
     * @return callable|array{object|class-string, non-empty-string}|class-string<object&callable>
     */
    public function getHandler(): callable|array|string
    {
        return $this->handler;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string[] $methods
     */
    public function methods(array $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return array<string, string|null>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    public function getParam(string $name, ?string $default = null): ?string
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * @param array<string, string|null> $defaults
     */
    public function defaults(array $defaults): self
    {
        $this->params = $defaults;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function hasConstraint(string $paramName): bool
    {
        return array_key_exists($paramName, $this->constraints);
    }

    public function getConstraint(string $paramName, ?string $default = null): ?string
    {
        return $this->constraints[$paramName] ?? $default;
    }

    /**
     * @param array<string, string> $constraints
     */
    public function constraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function host(?string $host): self
    {
        $this->host = $host;

        return $this;
    }
}

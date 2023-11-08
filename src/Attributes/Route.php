<?php

declare(strict_types=1);

namespace Ystrion\ViaRouter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @param string[] $methods
     * @param array<string, string> $constraints
     * @param array<string, string|null> $defaults
     * @param string|null $host
     */
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly array $methods = ['GET'],
        public readonly array $constraints = [],
        public readonly array $defaults = [],
        public readonly ?string $host = null
    ) {
    }
}

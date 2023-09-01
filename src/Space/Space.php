<?php
declare(strict_types=1);

namespace PhpStyler\Space;

abstract class Space
{
    public readonly string $method;

    public function __construct()
    {
        $this->method = lcfirst(ltrim((string) strrchr(get_class($this), '\\'), '\\'));
    }
}

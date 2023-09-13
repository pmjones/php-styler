<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class Nesting
{
    protected int $level = 0;

    protected array $types = [];

    public function incr(string $type) : void
    {
        $this->level ++;
        $this->types[$type] ??= 0;
        $this->types[$type] ++;
    }

    public function decr(string $type) : void
    {
        $this->level --;
        $this->types[$type] ??= 0;

        if (! $this->types[$type]) {
            throw new RuntimeException(
                "cannot decrease {$type} nesting level below zero",
            );
        }

        $this->types[$type] --;
    }

    public function in(string ...$types) : bool
    {
        foreach ($types as $type) {
            $this->types[$type] ??= 0;

            if ($this->types[$type]) {
                return true;
            }
        }

        return false;
    }

    public function notIn(string ...$types) : bool
    {
        foreach ($types as $type) {
            $this->types[$type] ??= 0;

            if ($this->types[$type]) {
                return false;
            }
        }

        return true;
    }

    public function level(string $type = null) : int
    {
        $this->types[$type] ??= 0;
        return $type ? $this->types[$type] : $this->level;
    }
}

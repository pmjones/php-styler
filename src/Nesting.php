<?php
declare(strict_types=1);

namespace PhpStyler;

class Nesting
{
    protected int $level = 0;

    /**
     * @var array<string, int>
     */
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
            throw new Exception("Cannot decrease {$type} nesting level below zero");
        }

        $this->types[$type] --;
    }

    public function in(string $type) : bool
    {
        $this->types[$type] ??= 0;
        return (bool) $this->types[$type];
    }

    public function level(string $type = null) : int
    {
        $this->types[$type] ??= 0;
        return $type ? $this->types[$type] : $this->level;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class State
{
    /**
     * @var int[]
     */
    protected array $args = [];

    public int $array = 0;

    public bool $atFirstInBody = false;

    public int $attrArgs = 0;

    public int $cond = 0;

    public int $encapsed = 0;

    public bool $hadAttribute = false;

    public bool $hadComment = false;

    public int $heredoc = 0;

    public int $instanceOp = 0;

    public int $params = 0;

    public int $ternary = 0;

    public int $concat = 0;

    public function __get(string $key) : mixed
    {
        throw new RuntimeException("No such property: {$key}");
    }

    public function __set(string $key, mixed $val) : void
    {
        throw new RuntimeException("No such property: {$key}");
    }

    public function __isset(string $key) : bool
    {
        throw new RuntimeException("No such property: {$key}");
    }

    public function __unset(string $key) : void
    {
        throw new RuntimeException("No such property: {$key}");
    }

    public function inArgs() : bool
    {
        return (bool) $this->args;
    }

    public function inArgsOrArray() : bool
    {
        return $this->args || $this->array;
    }

    public function increaseArgsLevel(?bool $expansive = false) : void
    {
        $level = count($this->args) + 1;
        $this->args[] = $level * ($expansive ? -1 : 1);
    }

    public function decreaseArgsLevel() : void
    {
        if (! $this->args) {
            throw new \Exception("cannot decrease args level below zero");
        }

        array_pop($this->args);
    }

    public function getArgsLevel() : int
    {
        return $this->args ? end($this->args) : 0;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class State
{
    public int $args = 0;

    public int $array = 0;

    public bool $atFirstInBody = false;

    public int $attrArgs = 0;

    public int $cond = 0;

    public int $encapsed = 0;

    public bool $hadAttribute = false;

    public bool $hadComment = false;

    public int $heredoc = 0;

    public int $instanceCall = 0;

    public int $instanceProp = 0;

    public int $param = 0;

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
}

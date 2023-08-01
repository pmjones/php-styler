<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Return_ extends Printable
{
    public function __construct(public readonly bool $isEmpty)
    {
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Continue_ extends Printable
{
    public function __construct(public readonly ?int $num)
    {
    }
}

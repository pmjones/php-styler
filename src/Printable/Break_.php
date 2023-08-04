<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Break_ extends Printable
{
    public function __construct(public readonly ?string $num)
    {
    }
}

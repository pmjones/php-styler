<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Goto_ extends Printable
{
    public function __construct(
        public readonly string $label,
    ) {
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Namespace_ extends Printable
{
    public function __construct(
        public readonly ?string $name,
    ) {
    }
}

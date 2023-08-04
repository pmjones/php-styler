<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Nowdoc extends Printable
{
    public function __construct(public string $label)
    {
    }
}

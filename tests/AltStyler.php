<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Printable as P;

class AltStyler extends Styler
{
    protected function sFunctionBody(P\Body $p) : void
    {
        $this->newline();
        $this->clip(
            condition: fn (string $lastLine) : bool
                => str_starts_with(trim($lastLine), ')'),
            append: ' ',
        );
        $this->line[] = '{';
        $this->newline();
        $this->indent();
    }

    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->line[] = ': ';
    }
}

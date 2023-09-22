<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Printable as P;

class ExtStyler extends Styler
{
    protected function functionBodyClipWhen() : callable
    {
        return fn (string $lastLine) : bool => str_starts_with(trim($lastLine), ')');
    }

    protected function sReturnType(P\ReturnType $p) : void
    {
        $this->line[] = ': ';
    }
}

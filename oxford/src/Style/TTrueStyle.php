<?php
declare(strict_types=1);

namespace Oxford\Style;

class TTrueStyle extends Style
{
    /** @var ?callable(string): string */
    public mixed $case = 'strtolower';
}

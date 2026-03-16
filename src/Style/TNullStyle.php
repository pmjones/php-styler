<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TNullStyle extends Style
{
    /**
     * @var ?callable(string): string
     */
    public mixed $case = 'strtolower';
}

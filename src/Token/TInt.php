<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;

class TInt extends T
{
    public function render(Line $line) : string
    {
        return 'int';
    }
}

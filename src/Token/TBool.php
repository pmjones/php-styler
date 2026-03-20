<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;

class TBool extends AToken
{
    public function render(Line $line) : string
    {
        return 'bool';
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;

interface ADocblock
{
    public function getDocblock() : Docblock;
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;

/**
 * Token: T_IS_NOT_EQUAL
 *
 * Syntax: != or <>
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TIsNotEqual extends T
{
    public function render(Line $line) : string
    {
        return '!=';
    }
}

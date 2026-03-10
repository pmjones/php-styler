<?php
declare(strict_types=1);

namespace Oxford\Token;

/**
 * Token: T_BOOLEAN_OR
 *
 * Syntax: ||
 *
 * Reference: https://www.php.net/manual/en/language.operators.logical.php logical operators
 */
class TBooleanOr extends T implements TSplittableOperator
{
    public function splitCategory() : int
    {
        return self::LOOSE_OPERATOR;
    }
}

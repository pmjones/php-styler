<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_BOOLEAN_AND
 *
 * Syntax: &&
 *
 * Reference: https://www.php.net/manual/en/language.operators.logical.php logical operators
 */
class TBooleanAnd extends T implements TSplittableOperator
{
    public function splitPointBefore() : ?TSplitPoint
    {
        return TSplitOperator::new (self::TIGHT_OPERATOR);
    }
}

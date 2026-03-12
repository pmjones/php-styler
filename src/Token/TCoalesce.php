<?php
declare(strict_types=1);

namespace PhpStyler\Token;

/**
 * Token: T_COALESCE
 *
 * Syntax: ??
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php#language.operators.comparison.coalesce comparison operators
 */
class TCoalesce extends T implements TSplittableOperator
{
    public function splitPointBefore() : ?TSplitPoint
    {
        return TSplitOperator::new (self::LOOSE_OPERATOR);
    }
}

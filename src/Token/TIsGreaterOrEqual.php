<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_IS_GREATER_OR_EQUAL
 *
 * Syntax: >=
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TIsGreaterOrEqual extends AToken implements ASplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitComparison(AToken::SYNTHETIC, '');
    }
}

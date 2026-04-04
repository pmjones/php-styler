<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_IS_IDENTICAL
 *
 * Syntax: ===
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TIsIdentical extends AToken implements
    ASplittableOperator,
    AComparisonOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitComparison(AToken::SYNTHETIC, '');
    }
}

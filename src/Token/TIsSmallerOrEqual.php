<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_IS_SMALLER_OR_EQUAL
 *
 * Syntax: <=
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TIsSmallerOrEqual extends AToken implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitComparison(AToken::SYNTHETIC, '');
    }
}

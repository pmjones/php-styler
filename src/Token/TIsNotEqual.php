<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;

/**
 * Token: T_IS_NOT_EQUAL
 *
 * Syntax: != or <>
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TIsNotEqual extends AToken implements ASplittableOperator, AComparisonOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitComparison(AToken::SYNTHETIC, '');
    }

    public function render(Line $line) : string
    {
        return '!=';
    }
}

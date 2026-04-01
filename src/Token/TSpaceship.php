<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_SPACESHIP
 *
 * Syntax: <=>
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php comparison operators
 */
class TSpaceship extends AToken implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitComparison(AToken::SYNTHETIC, '');
    }
}

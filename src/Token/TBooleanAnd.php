<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_BOOLEAN_AND
 *
 * Syntax: &&
 *
 * Reference: https://www.php.net/manual/en/language.operators.logical.php logical operators
 */
class TBooleanAnd extends AToken implements ASplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitBooleanAnd(AToken::SYNTHETIC, '');
    }
}

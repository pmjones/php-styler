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
class TBooleanAnd extends T implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return TSplitOperator::new (self::TIGHT_OPERATOR);
    }
}

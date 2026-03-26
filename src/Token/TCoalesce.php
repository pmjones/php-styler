<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

/**
 * Token: T_COALESCE
 *
 * Syntax: ??
 *
 * Reference: https://www.php.net/manual/en/language.operators.comparison.php#language.operators.comparison.coalesce comparison operators
 */
class TCoalesce extends AToken implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitLooseOperator(AToken::SYNTHETIC, '');
    }
}

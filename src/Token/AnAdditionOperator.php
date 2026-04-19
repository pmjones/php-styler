<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

abstract class AnAdditionOperator extends AToken implements ASplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitAddition(AToken::SYNTHETIC, '');
    }
}

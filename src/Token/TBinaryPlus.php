<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TBinaryPlus extends AToken implements ASplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitAddition(AToken::SYNTHETIC, '');
    }
}

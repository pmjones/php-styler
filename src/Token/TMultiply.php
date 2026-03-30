<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TMultiply extends AToken implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitTightOperator(AToken::SYNTHETIC, '');
    }
}

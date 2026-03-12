<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;

class TDot extends T implements TSplittableOperator
{
    public function splitBefore(Parser $parser) : ?TSplit
    {
        return TSplitOperator::new (self::TIGHT_OPERATOR);
    }
}

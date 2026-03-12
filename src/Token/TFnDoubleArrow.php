<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TFnDoubleArrow extends T implements TSplittableOperator
{
    public function splitBefore() : ?TSplit
    {
        return TSplitOperator::new (self::FN_DOUBLE_ARROW);
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

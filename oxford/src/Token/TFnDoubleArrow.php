<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TFnDoubleArrow extends T implements TSplittableOperator
{
    public function splitCategory() : int
    {
        return self::FN_DOUBLE_ARROW;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}

<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TElvisQuestion extends T implements TSplittableOperator
{
    public function splitCategory() : int
    {
        return self::LOOSE_OPERATOR;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}

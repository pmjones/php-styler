<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDefaultAfterCase extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->indentDecr();
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}

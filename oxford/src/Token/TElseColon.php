<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TElseColon extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $parser->addNesting($unparsed, static::class);
        $parser->space();
        $parser->indentIncr();
    }
}

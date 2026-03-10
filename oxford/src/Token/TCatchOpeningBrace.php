<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TCatchOpeningBrace extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, static::class);
        $parser->indentIncr();
    }
}

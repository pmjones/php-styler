<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDeclareOpeningBrace extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, static::class);
        $parser->indentIncr();
    }
}

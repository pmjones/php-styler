<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForeachColon extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->addNesting($source, static::class);
        $parser->space();
        $parser->indentIncr();
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForColon extends AToken implements AnOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, static::class);

        $parser->indentIncr();
    }
}

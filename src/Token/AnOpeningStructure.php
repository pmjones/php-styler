<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

abstract class AnOpeningStructure extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, static::class);
        $parser->indentIncr();
    }
}

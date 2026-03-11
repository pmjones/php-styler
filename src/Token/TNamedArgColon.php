<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TNamedArgColon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->add($source, static::class);
        $parser->space();
    }
}

<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TStaticComma extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->add($unparsed, static::class);
    }
}

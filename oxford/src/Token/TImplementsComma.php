<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TImplementsComma extends T implements TSplittableComma
{
    public function splitCategory() : int
    {
        return self::COMMA;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->add($unparsed, static::class);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArgsComma extends T implements TSplittableComma
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

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyComma extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, self::class);
    }
}

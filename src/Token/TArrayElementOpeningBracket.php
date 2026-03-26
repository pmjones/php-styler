<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayElementOpeningBracket extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $class = $parser->inEncapsedString()
            ? TEncapsedArrayElementOpeningBracket::class
            : self::class;
        $parser->addNesting($source, $class);
    }
}

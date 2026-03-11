<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TWhileClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TWhileOpeningParen::class);

        if ($parser->atNesting(TWhile::class) && $parser->getNextSource()?->is(';')) {
            $parser->popNesting(TWhile::class);
        }

        if (! $parser->getNextSource()?->is(['{', ':', ';'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}

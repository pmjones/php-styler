<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElvisColon extends AToken implements ATernaryNesting
{
    public const END_SEMICOLON = TElvisEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TElvisQuestion::class);

        $parser->addNesting($source, self::class);
    }
}

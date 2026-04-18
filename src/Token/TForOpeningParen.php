<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForOpeningParen extends AToken implements AConditionOpener
{
    public const END_SEMICOLON = TForSemicolon::class;

    public const EXPAND_PRIORITY = ASplittable::CONDITION_PAREN;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHookSet extends AToken
{
    public const OPENING_BRACE = TPropertyHookSetOpeningBrace::class;

    public const CLOSING_BRACE = TPropertyHookSetClosingBrace::class;

    public const END_SEMICOLON = TPropertyHookSetSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

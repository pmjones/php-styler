<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHookGet extends AToken
{
    public const OPENING_BRACE = TPropertyHookGetOpeningBrace::class;

    public const CLOSING_BRACE = TPropertyHookGetClosingBrace::class;

    public const END_SEMICOLON = TPropertyHookGetSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

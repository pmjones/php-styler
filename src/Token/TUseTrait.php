<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseTrait extends AToken
{
    public const OPENING_BRACE = TUseTraitOpeningBrace::class;

    public const CLOSING_BRACE = TUseTraitClosingBrace::class;

    public const END_SEMICOLON = TUseTraitEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

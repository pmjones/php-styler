<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Syntax: new class
 */
class TAnonymousClass extends AToken
{
    public const OPENING_BRACE = TAnonymousOpeningBrace::class;

    public const CLOSING_BRACE = TAnonymousClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

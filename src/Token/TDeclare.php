<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DECLARE
 *
 * Syntax: declare
 *
 * Reference: https://www.php.net/manual/en/control-structures.declare.php declare
 */
class TDeclare extends AToken
{
    public const OPENING_BRACE = TDeclareOpeningBrace::class;

    public const CLOSING_BRACE = TDeclareClosingBrace::class;

    public const END_SEMICOLON = TDeclareEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

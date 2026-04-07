<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_USE (file-level import)
 *
 * Syntax: use Foo\Bar;
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TUse extends AToken
{
    public const OPENING_BRACE = TUseOpeningBrace::class;

    public const CLOSING_BRACE = TUseClosingBrace::class;

    public const END_SEMICOLON = TUseEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TClasslikeOpeningBrace::class)) {
            $parser->parse($source, TUseTrait::class);
            return;
        }

        if ($parser->atNesting(TAnonymousFunction::class)) {
            $parser->parse($source, TUseVariables::class);
            return;
        }

        $parser->addNesting($source, self::class);
    }
}

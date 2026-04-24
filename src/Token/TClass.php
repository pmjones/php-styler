<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CLASS
 *
 * Syntax: class
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TClass extends AToken
{
    public const OPENING_BRACE = TClassOpeningBrace::class;

    public const CLOSING_BRACE = TClassClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev?->is(T_NEW)) {
            $parser->parse($source, TAnonymousClass::class);
            return;
        }

        $parser->addNesting($source, self::class);
    }
}

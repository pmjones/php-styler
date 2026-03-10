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
class TClass extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev?->is(T_NEW)) {
            $parser->parse($unparsed, TAnonymousClass::class);
            return;
        }

        if ($prev?->is('::')) {
            $parser->parse($unparsed, TString::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}

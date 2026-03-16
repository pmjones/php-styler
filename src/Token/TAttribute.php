<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ATTRIBUTE
 *
 * Syntax: #[
 *
 * Reference: https://www.php.net/manual/en/language.attributes.php attributes (available as of PHP 8.0.0)
 */
class TAttribute extends T implements TAttribution
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TParamsOpeningParen::class)) {
            $parser->parse($source, TInlineAttribute::class);
            return;
        }

        $parser->addNesting($source, self::class);
    }
}

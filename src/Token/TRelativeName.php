<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NAME_RELATIVE
 *
 * Syntax: namespace\Namespace
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces (available as of PHP 8.0.0)
 */
class TRelativeName extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if (
            $parser->getNextUnparsed()?->is('(')
            && ! $parser
                ->getPrevParsed()
                ?->is([
                    T_OBJECT_OPERATOR,
                    T_NULLSAFE_OBJECT_OPERATOR,
                    T_DOUBLE_COLON,
                    T_NEW,
                ])
            && ! $parser->atNesting(TAttribute::class)
        ) {
            $parser->add($unparsed, TFunctionCallRelative::class);
            $parser->space();
            return;
        }

        $parser->add($unparsed, self::class);
        $parser->space();
    }
}

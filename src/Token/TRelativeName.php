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
class TRelativeName extends AToken implements AType
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->source->peek()?->is('(')
            && ! $parser->getPrevParsed()
                ?->is([
                    T_OBJECT_OPERATOR,
                    T_NULLSAFE_OBJECT_OPERATOR,
                    T_DOUBLE_COLON,
                    T_NEW,
                ])
            && ! $parser->atNesting(AnAttribute::class)
        ) {
            $parser->add($source, TFunctionCallRelative::class);
            return;
        }

        $parser->add($source, self::class);
    }
}

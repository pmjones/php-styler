<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NAME_QUALIFIED
 *
 * Syntax: App\Namespace
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces (available as of PHP 8.0.0)
 */
class TQualifiedName extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->getNextSource()?->is('(')
            && ! $parser->getPrevParsed()
                ?->is([
                    T_NEW,
                    T_OBJECT_OPERATOR,
                    T_NULLSAFE_OBJECT_OPERATOR,
                    T_DOUBLE_COLON,
                ])
            && ! $parser->atNesting(TAttribution::class)
        ) {
            $parser->add($source, TFunctionCallQualified::class);
            return;
        }

        $parser->add($source, self::class);
    }
}

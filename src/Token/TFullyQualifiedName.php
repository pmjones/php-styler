<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NAME_FULLY_QUALIFIED
 *
 * Syntax: \App\Namespace
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces (available as of PHP 8.0.0)
 */
class TFullyQualifiedName extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->atNesting(TUse::class)
            || $parser->atNesting(TUseFunction::class)
            || $parser->atNesting(TUseConst::class)
        ) {
            $source->text = ltrim($source->text, '\\');
            $parser->add($source, TQualifiedName::class);
            return;
        }

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
            $parser->add($source, TFunctionCallFullyQualified::class);
            return;
        }

        $parser->add($source, self::class);
    }
}

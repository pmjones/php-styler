<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG
 *
 * Syntax: &
 *
 * Reference: https://www.php.net/manual/en/language.types.declarations.php Type declarations (available as of PHP 8.1.0)
 */
class TAmpersandNotFollowedByVarOrVararg extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        $class = match (true) {
            $prev instanceof TUnqualifiedName,
            $prev instanceof TQualifiedName,
            $prev instanceof TFullyQualifiedName,
            $prev instanceof TRelativeName,
            $prev instanceof TSelf,
            $prev instanceof TParent,
            $prev instanceof TUnknownString => TIntersection::class,

            $prev instanceof TFunction,
            $prev instanceof TFn,
            $prev instanceof TAnonymousFunction => TReference::class,

            default => TBitwiseAnd::class,
        };

        $parser->add($source, $class);

        if ($class === TReference::class) {
            $parser->reclassifyNextSourceAsName();
        }
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPipe extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        $class = match (true) {
            $prev instanceof TInt,
            $prev instanceof TFloat,
            $prev instanceof TBool,
            $prev instanceof TVoid,
            $prev instanceof TNever,
            $prev instanceof TMixed,
            $prev instanceof TIterable,
            $prev instanceof TObject,
            $prev instanceof TTrue,
            $prev instanceof TFalse,
            $prev instanceof TNull,
            $prev instanceof TCallable => TUnion::class,

            $prev instanceof TUnqualifiedName,
            $prev instanceof TQualifiedName,
            $prev instanceof TFullyQualifiedName,
            $prev instanceof TRelativeName,
            $prev instanceof TSelf,
            $prev instanceof TParent,
            $prev instanceof TUnknownString,
            $prev instanceof TString => self::isTypeContext($parser)
                ? TUnion::class
                : TBitwiseOr::class,

            default => TBitwiseOr::class,
        };

        $parser->add($source, $class);
    }

    private static function isTypeContext(Parser $parser) : bool
    {
        $prevPrev = $parser->getPrevParsed(skip: 1);

        return $prevPrev instanceof TReturnColon
            || $prevPrev instanceof TUnion
            || $prevPrev instanceof TIntersection
            || $prevPrev instanceof TNullable
            || $prevPrev instanceof AModifier
            || $prevPrev instanceof TParamsOpeningParen
            || $prevPrev instanceof TParamsComma;
    }
}

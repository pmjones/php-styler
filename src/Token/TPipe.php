<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPipe extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
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

        $parser->add($unparsed, $class);

        if ($class !== TUnion::class) {
            $parser->space();
        }
    }

    private static function isTypeContext(Parser $parser) : bool
    {
        $prevPrev = $parser->getPrevParsed(skip: 1);

        return $prevPrev instanceof TReturnColon
            || $prevPrev instanceof TUnion
            || $prevPrev instanceof TIntersection
            || $prevPrev instanceof TNullable
            || $prevPrev instanceof TPublic
            || $prevPrev instanceof TProtected
            || $prevPrev instanceof TPrivate
            || $prevPrev instanceof TReadonly
            || $prevPrev instanceof TStatic
            || $prevPrev instanceof TVar
            || $prevPrev instanceof TParamsOpeningParen
            || $prevPrev instanceof TParamsComma;
    }
}

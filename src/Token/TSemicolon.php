<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TSemicolon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TOpeningBraceless::class)) {
            $parser->parse($source, TClosingBraceless::class);
            return;
        }

        // TConst conditional: class body vs namespace
        if ($parser->getNesting() === TConst::class) {
            $parseClass = (
                    $parser->atNesting(TConst::class, TClasslikeOpeningBrace::class)
                    || $parser->atNesting(
                        TConst::class,
                        TAnonymousOpeningBrace::class,
                    )
                )
                ? TConstEndSemicolon::class
                : TNamespaceConstEndSemicolon::class;

            $parser->parse($source, $parseClass);

            if ($parser->atNesting(TOpeningBraceless::class)) {
                $parser->endBracelessBody($source);
            }

            return;
        }

        // TReturnColon conditional: magic vs regular
        if ($parser->getNesting() === TReturnColon::class) {
            $parseClass = $parser->atNesting(
                    TReturnColon::class,
                    TMagicMethod::class,
                )
                ? TAbstractMagicMethodEndSemicolon::class
                : TAbstractMethodEndSemicolon::class;

            $parser->parse($source, $parseClass);

            if ($parser->atNesting(TOpeningBraceless::class)) {
                $parser->endBracelessBody($source);
            }

            return;
        }

        // lookup from nesting token constant
        $parseClass = $parser->getNestingEndSemicolon();

        if ($parseClass !== null) {
            $parser->parse($source, $parseClass);

            // pop stacked statement-level nesting left after ternary/elvis
            // (e.g., echo $x ? "a" : "b"; leaves TEcho after TTernaryColon pops)
            if ($parser->atNesting(AStatementNesting::class)) {
                $parser->popNesting($parser->getNesting());
            }

            if ($parser->atNesting(TOpeningBraceless::class)) {
                $parser->endBracelessBody($source);
            }

            return;
        }

        $parser->add($source, self::class);
    }
}

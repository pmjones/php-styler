<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TOpeningBraceless::class)) {
            $parser->parse($source, TClosingBraceless::class);
            return;
        }

        $parseClass = match ($parser->getNesting()) {
            TEnumCase::class => TEnumCaseEndSemicolon::class,
            TDeclare::class => TDeclareEndSemicolon::class,
            TNamespace::class => TNamespaceEndSemicolon::class,
            TPropertyHookGet::class,
            TPropertyHookGetDoubleArrow::class => TPropertyHookGetSemicolon::class,
            TPropertyHookSet::class,
            TPropertyHookSetDoubleArrow::class => TPropertyHookSetSemicolon::class,
            TConst::class => $parser
                ->atNesting(TConst::class, TClasslikeOpeningBrace::class)
                ? TConstEndSemicolon::class
                : TNamespaceConstEndSemicolon::class,
            TUse::class,
            TUseFunction::class,
            TUseConst::class => TUseEndSemicolon::class,
            TEcho::class => TEchoEndSemicolon::class,
            TElvisColon::class => TElvisEndSemicolon::class,
            TTernaryColon::class => TTernaryEndSemicolon::class,
            TFnDoubleArrow::class => TFnEndSemicolon::class,
            TReturnColon::class, TFunction::class => TAbstractMethodEndSemicolon::class,
            TUseTrait::class => TUseTraitEndSemicolon::class,
            TYield::class => TYieldEndSemicolon::class,
            TGlobal::class => TGlobalEndSemicolon::class,
            TStaticVar::class => TStaticVarEndSemicolon::class,
            THaltCompiler::class => THaltCompilerSemicolon::class,
            TForOpeningParen::class => TForSemicolon::class,
            TClassOpeningBrace::class,
            TEnumOpeningBrace::class,
            TInterfaceOpeningBrace::class,
            TTraitOpeningBrace::class => TPropertyEndSemicolon::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($source, $parseClass);
            return;
        }

        $parser->add($source, self::class);
    }
}

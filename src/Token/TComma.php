<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TComma extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popTernaryNesting();

        $parseClass = match ($parser->getNesting()) {
            TParamsOpeningParen::class => TParamsComma::class,
            TArgsOpeningParen::class => TArgsComma::class,
            TArrayConstructOpeningParen::class,
            TArrayOpeningBracket::class => TArrayComma::class,
            TForOpeningParen::class => TForComma::class,
            TUseVariablesOpeningParen::class => TUseVariablesComma::class,
            TDeclareDirectivesOpeningParen::class => TDeclareDirectivesComma::class,
            TUse::class,
            TUseFunction::class,
            TUseConst::class => TUseComma::class,
            TUseTrait::class => TUseTraitComma::class,
            TUseTraitOpeningBrace::class => TInsteadofComma::class,
            TGlobal::class => TGlobalComma::class,
            TStaticVar::class => TStaticComma::class,
            TClass::class,
            TEnum::class => TImplementsComma::class,
            TInterface::class => TExtendsComma::class,
            TAttribute::class => TAttributeComma::class,
            TMatchDoubleArrow::class => TMatchReturnComma::class,
            TMatchOpeningBrace::class => TMatchArmComma::class,
            TEcho::class => TEchoComma::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($unparsed, $parseClass);
            return;
        }

        $parser->add($unparsed, self::class);
    }
}

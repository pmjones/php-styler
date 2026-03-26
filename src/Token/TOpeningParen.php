<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parseClass = match ($parser->getNesting()) {
            TArrayConstruct::class => TArrayConstructOpeningParen::class,

            TDeclare::class => TDeclareDirectivesOpeningParen::class,

            TUse::class => TUseVariablesOpeningParen::class,

            TElseif::class => TElseifOpeningParen::class,
            TIf::class => TIfOpeningParen::class,
            TMatch::class => TMatchOpeningParen::class,
            TSwitch::class => TSwitchOpeningParen::class,

            TFor::class => TForOpeningParen::class,
            TForeach::class => TForeachOpeningParen::class,
            TWhile::class => TWhileOpeningParen::class,

            TCatch::class,
            TFn::class,
            TFunction::class,
            TAnonymousFunction::class => TParamsOpeningParen::class,

            TAnonymousClass::class => TAnonymousClassArgsOpeningParen::class,

            TAttribute::class, TInlineAttribute::class => TArgsOpeningParen::class,

            TPropertyHookSet::class => TPropertyHookSetOpeningParen::class,

            default => null,
        };

        if ($parseClass) {
            $parser->parse($source, $parseClass);
            return;
        }

        if (
            $parser
            ->getPrevParsed()
            ?->is([
                T_EMPTY,
                T_EVAL,
                T_EXIT,
                T_HALT_COMPILER,
                T_ISSET,
                T_LIST,
                T_NAME_FULLY_QUALIFIED,
                T_NAME_QUALIFIED,
                T_NAME_RELATIVE,
                T_STATIC,
                T_STRING,
                T_UNSET,
                T_VARIABLE,
                ')',
                ']',
                '}',
            ])
        ) {
            $parser->parse($source, TArgsOpeningParen::class);
            return;
        }

        $parser->parse($source, TExpressionOpeningParen::class);
    }
}

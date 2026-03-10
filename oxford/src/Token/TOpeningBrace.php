<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TOpeningBrace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TDollar) {
            $parser->addNesting($unparsed, TDynamicVariableOpeningBrace::class);
            return;
        }

        if (
            $prev instanceof TObjectOperator
            || $prev instanceof TNullsafeObjectOperator
            || $prev instanceof TMemberDoubleColon
        ) {
            $parser->addNesting($unparsed, TDynamicMemberOpeningBrace::class);
            return;
        }

        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        if ($parser->getNesting() === TFunction::class) {
            $parser->parse($unparsed, TFunctionOpeningBrace::class);
            return;
        }

        $braceClass = match ($parser->getNesting()) {
            TClass::class => TClassOpeningBrace::class,
            TEnum::class => TEnumOpeningBrace::class,
            TInterface::class => TInterfaceOpeningBrace::class,
            TTrait::class => TTraitOpeningBrace::class,
            TAnonymousClass::class,
            TAnonymousFunction::class => TAnonymousOpeningBrace::class,
            TCatch::class => TCatchOpeningBrace::class,
            TDo::class => TDoOpeningBrace::class,
            TElse::class => TElseOpeningBrace::class,
            TElseif::class => TElseifOpeningBrace::class,
            TFinally::class => TFinallyOpeningBrace::class,
            TFor::class => TForOpeningBrace::class,
            TForeach::class => TForeachOpeningBrace::class,
            TIf::class => TIfOpeningBrace::class,
            TMatch::class => TMatchOpeningBrace::class,
            TSwitch::class => TSwitchOpeningBrace::class,
            TTry::class => TTryOpeningBrace::class,
            TWhile::class => TWhileOpeningBrace::class,
            TDeclare::class => TDeclareOpeningBrace::class,
            TNamespace::class => TNamespaceOpeningBrace::class,
            TUseTrait::class => TUseTraitOpeningBrace::class,
            TPropertyHookGet::class => TPropertyHookGetOpeningBrace::class,
            TPropertyHookSet::class => TPropertyHookSetOpeningBrace::class,
            default => null,
        };

        if ($braceClass) {
            $parser->parse($unparsed, $braceClass);
            return;
        }

        $useClass = match ($parser->getNesting()) {
            TUse::class => TUseOpeningBrace::class,
            TUseFunction::class => TUseFunctionOpeningBrace::class,
            TUseConst::class => TUseConstOpeningBrace::class,
            default => null,
        };

        if ($useClass) {
            $parser->add($unparsed, $useClass);
            return;
        }

        if (
            $parser->getPrevParsed() instanceof TVariable
            && $parser->atNesting(TClasslikeOpeningBrace::class)
        ) {
            $parser->parse($unparsed, TPropertyHooksOpeningBrace::class);
            return;
        }

        $parser->add($unparsed, self::class);
    }
}

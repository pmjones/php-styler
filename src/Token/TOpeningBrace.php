<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningBrace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev instanceof TDollar) {
            $parser->addNesting($source, TDynamicVariableOpeningBrace::class);
            return;
        }

        if (
            $prev instanceof TObjectOperator
            || $prev instanceof TNullsafeObjectOperator
            || $prev instanceof TMemberDoubleColon
        ) {
            $parser->addNesting($source, TDynamicMemberOpeningBrace::class);
            return;
        }

        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        if ($parser->getNesting() === TFunction::class) {
            $parser->parse($source, TFunctionOpeningBrace::class);
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
            $parser->parse($source, $braceClass);
            return;
        }

        $useClass = match ($parser->getNesting()) {
            TUse::class => TUseOpeningBrace::class,
            TUseFunction::class => TUseFunctionOpeningBrace::class,
            TUseConst::class => TUseConstOpeningBrace::class,
            default => null,
        };

        if ($useClass) {
            $parser->add($source, $useClass);
            return;
        }

        if (
            $parser->getPrevParsed() instanceof TVariable
            && $parser->atNesting(TClasslikeOpeningBrace::class)
        ) {
            $parser->parse($source, TPropertyHooksOpeningBrace::class);
            return;
        }

        $parser->add($source, self::class);
    }
}

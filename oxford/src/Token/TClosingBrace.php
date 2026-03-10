<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TClosingBrace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popTernaryNesting();

        $curly = match ($parser->getNesting()) {
            TCurlyOpen::class => TCurlyClose::class,
            TDollarOpenCurlyBraces::class => TDollarCloseCurlyBraces::class,
            TDynamicVariableOpeningBrace::class => TDynamicVariableClosingBrace::class,
            TDynamicMemberOpeningBrace::class => TDynamicMemberClosingBrace::class,
            default => null,
        };

        if ($curly) {
            $parser->parse($unparsed, $curly);
            return;
        }

        if ($parser->atNesting(TMatchDoubleArrow::class)) {
            $parser->popNesting(TMatchDoubleArrow::class);
        }

        $closesCase = false;

        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);
            $parser->popNesting(TCase::class, TDefaultCase::class, TCaseAfterCase::class, TDefaultAfterCase::class);
            $closesCase = true;
        }

        $nesting = $parser->getNesting();

        $useClass = match ($nesting) {
            TUse::class => TUseClosingBrace::class,
            TUseFunction::class => TUseFunctionClosingBrace::class,
            TUseConst::class => TUseConstClosingBrace::class,
            default => null,
        };

        if ($useClass) {

            $parser->add($unparsed, $useClass);

            return;
        }

        if ($parser->atNesting(TPropertyHooksOpeningBrace::class)) {
            $parser->parse($unparsed, TPropertyHooksClosingBrace::class);
            return;
        }

        if ($parser->atNesting(TOpeningStructure::class)) {
            $parser->popNesting($parser->getNesting());
        }

        $nesting = $parser->getNesting();

        if (
            $parser->getNextUnparsed()
                ?->is([
                    T_CATCH,
                    T_ELSE,
                    T_ELSEIF,
                    T_FINALLY,
                ])
            || $nesting === TDo::class
            || $nesting === TTry::class
        ) {
            $parser->parse($unparsed, TContinuationBrace::class);
            return;
        }

        $nestedBraceClass = match ($nesting) {
            // classlike
            TClass::class => TClassClosingBrace::class,
            TEnum::class => TEnumClosingBrace::class,
            TInterface::class => TInterfaceClosingBrace::class,
            TTrait::class => TTraitClosingBrace::class,

            // anonymous
            TAnonymousClass::class,
            TAnonymousFunction::class => TAnonymousClosingBrace::class,

            // function
            TFunction::class => TFunctionClosingBrace::class,

            // control
            TCatch::class => TCatchClosingBrace::class,
            TElse::class => TElseClosingBrace::class,
            TElseif::class => TElseifClosingBrace::class,
            TFinally::class => TFinallyClosingBrace::class,
            TFor::class => TForClosingBrace::class,
            TForeach::class => TForeachClosingBrace::class,
            TIf::class => TIfClosingBrace::class,
            TMatch::class => TMatchClosingBrace::class,
            TSwitch::class => $closesCase ? TSwitchAfterCaseClosingBrace::class : TSwitchClosingBrace::class,
            TWhile::class => TWhileClosingBrace::class,

            // other
            TDeclare::class => TDeclareClosingBrace::class,
            TNamespace::class => TNamespaceClosingBrace::class,
            TUseTrait::class => TUseTraitClosingBrace::class,

            // property hooks
            TPropertyHookGet::class => TPropertyHookGetClosingBrace::class,
            TPropertyHookSet::class => TPropertyHookSetClosingBrace::class,

            // none
            default => null,
        };

        if ($nestedBraceClass) {
            $parser->parse($unparsed, $nestedBraceClass);
            return;
        }

        $parser->add($unparsed, self::class);
    }
}

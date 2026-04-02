<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingBrace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        $curly = match ($parser->getNesting()) {
            TCurlyOpen::class => TCurlyClose::class,
            TDollarOpenCurlyBraces::class => TDollarCloseCurlyBraces::class,

            TDynamicVariableOpeningBrace::class
                => TDynamicVariableClosingBrace::class,

            TDynamicMemberOpeningBrace::class => TDynamicMemberClosingBrace::class,
            default => null,
        };

        if ($curly) {
            $parser->parse($source, $curly);
            return;
        }

        if ($parser->atNesting(TMatchDoubleArrow::class)) {
            $parser->popNesting(TMatchDoubleArrow::class);
        }

        $closesCase = false;

        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);

            $parser->popNesting(
                TCase::class,
                TDefaultCase::class,
                TCaseAfterCase::class,
                TDefaultAfterCase::class,
            );

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
            $parser->add($source, $useClass);

            return;
        }

        if ($parser->atNesting(TPropertyHooksAbstractOpeningBrace::class)) {
            $parser->parse($source, TPropertyHooksAbstractClosingBrace::class);
            return;
        }

        if ($parser->atNesting(TPropertyHooksOpeningBrace::class)) {
            $parser->parse($source, TPropertyHooksClosingBrace::class);
            return;
        }

        if ($parser->atNesting(TOpeningStructure::class)) {
            $parser->popNesting($parser->getNesting());
        }

        $nesting = $parser->getNesting();

        if (
            $parser->getNextSource()?->is([T_CATCH, T_ELSE, T_ELSEIF, T_FINALLY])
            || $nesting === TDo::class
            || $nesting === TTry::class
        ) {
            $parser->parse($source, TContinuationBrace::class);
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

            TSwitch::class => $closesCase
                ? TSwitchAfterCaseClosingBrace::class
                : TSwitchClosingBrace::class,

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
            $parser->parse($source, $nestedBraceClass);
            return;
        }

        $parser->add($source, self::class);
    }
}

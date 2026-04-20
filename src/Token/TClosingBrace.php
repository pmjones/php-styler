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

        // use-import braces use add() instead of parse()
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

        if ($parser->atNesting(AnOpeningStructure::class)) {
            $parser->popNesting($parser->getNesting());
        }

        $nesting = $parser->getNesting();

        if (
            $parser->source->peek()?->is([T_CATCH, T_ELSE, T_ELSEIF, T_FINALLY])
            || $nesting === TDo::class
            || $nesting === TTry::class
        ) {
            $parser->parse($source, TContinuationBrace::class);
            return;
        }

        // TSwitch conditional: depends on case state
        if ($nesting === TSwitch::class) {
            $braceClass = $closesCase
                ? TSwitchAfterCaseClosingBrace::class
                : TSwitchClosingBrace::class;

            $parser->parse($source, $braceClass);
            return;
        }

        // lookup from nesting token constant
        $braceClass = $parser->nestingStack->getClosingBrace();

        if ($braceClass !== null) {
            $parser->parse($source, $braceClass);
            return;
        }

        $parser->add($source, self::class);
    }
}

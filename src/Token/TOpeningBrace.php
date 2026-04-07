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

        // use-import braces use add() instead of parse()
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

        // property hooks after variable in class body
        if (
            $parser->getPrevParsed() instanceof TVariable
            && $parser->atNesting(TClasslikeOpeningBrace::class)
        ) {
            $parser->parse($source, TPropertyHooksOpeningBrace::class);
            return;
        }

        // lookup from nesting token constant
        $braceClass = $parser->getNestingOpeningBrace();

        if ($braceClass !== null) {
            $parser->parse($source, $braceClass);
            return;
        }

        $parser->add($source, self::class);
    }
}

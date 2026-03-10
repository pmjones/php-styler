<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Syntax: =
 */
class TAssign extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parseClass = match ($parser->getNesting()) {
            TDeclareDirectivesOpeningParen::class => TAssignDirective::class,
            TParamsOpeningParen::class => TAssignDefault::class,
            TConst::class => TAssignConst::class,
            TClassOpeningBrace::class,
            TEnumOpeningBrace::class,
            TInterfaceOpeningBrace::class,
            TTraitOpeningBrace::class => TAssignProperty::class,
            default => self::class,
        };

        if ($parseClass === TAssignDirective::class) {
            $parser->noSpace();
        }

        $parser->add($unparsed, $parseClass);
    }
}

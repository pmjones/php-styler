<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Syntax: =
 */
class TAssign extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
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

        $parser->add($source, $parseClass);
    }
}

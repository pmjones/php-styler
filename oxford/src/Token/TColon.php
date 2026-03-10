<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Exception;
use Oxford\Parser;
use PhpToken;

class TColon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if (
            $parser->atNesting(TCase::class)
            || $parser->atNesting(TDefaultCase::class)
            || $parser->atNesting(TCaseAfterCase::class)
            || $parser->atNesting(TDefaultAfterCase::class)
        ) {
            $parser->parse($unparsed, TCaseColon::class);
            return;
        }

        $nesting = $parser->getNesting();

        $colonClass = match ($nesting) {
            TDeclare::class => TDeclareColon::class,
            TElse::class => TElseColon::class,
            TElseif::class => TElseifColon::class,
            TFor::class => TForColon::class,
            TForeach::class => TForeachColon::class,
            TIf::class => TIfColon::class,
            TSwitch::class => TSwitchColon::class,
            TWhile::class => TWhileColon::class,
            default => null,
        };

        if ($colonClass) {
            $parser->parse($unparsed, $colonClass);
            return;
        }

        $parseClass = match ($nesting) {
            TEnum::class => TEnumBackedColon::class,
            TFn::class,
            TFunction::class,
            TAnonymousFunction::class => TReturnColon::class,
            TElvisQuestion::class => TElvisColon::class,
            TTernaryQuestion::class => TTernaryColon::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($unparsed, $parseClass);
            return;
        }

        if ($parser->getPrevParsed() instanceof TGotoLabel) {
            $parser->add($unparsed, TGotoLabelColon::class);
            return;
        }

        if ($parser->atNesting(TArgsOpeningParen::class)) {
            $parser->add($unparsed, TNamedArgColon::class);
            return;
        }

        $message = "Unknown kind of colon "
            . "on line {$unparsed->line} "
            . "at position {$unparsed->pos} "
            . "in nesting "
            . var_export($parser->listNesting(), true);

        throw new Exception($message);
    }
}

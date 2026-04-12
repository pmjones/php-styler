<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TColon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $nesting = $parser->getNesting();

        while (
            $nesting === TTernaryColon::class || $nesting === TElvisColon::class
        ) {
            $parser->popNesting($nesting);
            $nesting = $parser->getNesting();
        }

        if (
            $parser->atNesting(TCase::class)
            || $parser->atNesting(TDefaultCase::class)
            || $parser->atNesting(TCaseAfterCase::class)
            || $parser->atNesting(TDefaultAfterCase::class)
        ) {
            $parser->parse($source, TCaseColon::class);
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
            $parser->parse($source, $colonClass);
            return;
        }

        $parseClass = match ($nesting) {
            TEnum::class => TEnumBackedColon::class,

            TFn::class,
            TFunction::class,
            TMagicMethod::class,
            TAnonymousFunction::class => TReturnColon::class,

            TElvisQuestion::class => TElvisColon::class,
            TTernaryQuestion::class => TTernaryColon::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($source, $parseClass);
            return;
        }

        if ($parser->getPrevParsed() instanceof TGotoLabel) {
            $parser->add($source, TGotoLabelColon::class);
            return;
        }

        if ($parser->atNesting(TArgsOpeningParen::class)) {
            $parser->add($source, TNamedArgColon::class);
            return;
        }

        $message = "Unknown kind of colon "
            . "on line {$source->line} "
            . "at position {$source->pos} "
            . "in nesting "
            . var_export($parser->listNesting(), true);

        throw new Exception($message);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TClosingBraceless extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getNextSource()?->is([T_ELSE, T_ELSEIF])) {
            $parser->parse($source, TContinuationBraceless::class);
            return;
        }

        $parser->noSpace();
        $parser->add($source, TSemicolon::class);
        $parser->space();
        $parser->popNesting(TOpeningBraceless::class);

        $nesting = $parser->getNesting();

        $closingBraceless = match ($nesting) {
            TIf::class => TIfClosingBraceless::class,
            TElse::class => TElseClosingBraceless::class,
            TElseif::class => TElseifClosingBraceless::class,
            TWhile::class => TWhileClosingBraceless::class,
            TFor::class => TForClosingBraceless::class,
            TForeach::class => TForeachClosingBraceless::class,
            default => null,
        };

        if ($closingBraceless) {
            $parser->parse($source, $closingBraceless);
            return;
        }

        $message = "Unknown kind of closing braceless "
            . "on line {$source->line} "
            . "at position {$source->pos} "
            . "in nesting "
            . var_export($parser->listNesting(), true);

        throw new Exception($message);
    }
}

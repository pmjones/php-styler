<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Exception;
use Oxford\Parser;
use PhpToken;

class TContinuationBraceless extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->add($unparsed, TSemicolon::class);
        $parser->space();
        $parser->popNesting(TOpeningBraceless::class);

        $nesting = $parser->getNesting();

        $partingBraceless = match ($nesting) {
            TIf::class => TIfContinuationBraceless::class,
            TElseif::class => TElseifContinuationBraceless::class,
            default => null,
        };

        if ($partingBraceless) {
            $parser->parse($unparsed, $partingBraceless);
            return;
        }

        $message = "Unknown kind of parting braceless "
            . "on line {$unparsed->line} "
            . "at position {$unparsed->pos} "
            . "in nesting "
            . var_export($parser->listNesting(), true);

        throw new Exception($message);
    }
}

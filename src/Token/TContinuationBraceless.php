<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TContinuationBraceless extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, TSemicolon::class);
        $parser->popNesting(TOpeningBraceless::class);

        $nesting = $parser->getNesting();

        $partingBraceless = match ($nesting) {
            TIf::class => TIfContinuationBraceless::class,
            TElseif::class => TElseifContinuationBraceless::class,
            default => null,
        };

        if ($partingBraceless) {
            $parser->parse($source, $partingBraceless);
            return;
        }

        $message = "Unknown kind of parting braceless"
            . " on line {$source->line}"
            . " at position {$source->pos}";

        throw Exception::fromParser($message, $parser, $source);
    }
}

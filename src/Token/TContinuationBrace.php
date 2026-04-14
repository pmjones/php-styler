<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TContinuationBrace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $nesting = $parser->getNesting();

        $partingBrace = match ($nesting) {
            TIf::class => TIfContinuationBrace::class,
            TElseif::class => TElseifContinuationBrace::class,
            TTry::class => TTryContinuationBrace::class,
            TCatch::class => TCatchContinuationBrace::class,
            TDo::class => TDoContinuationBrace::class,
            default => null,
        };

        if ($partingBrace) {
            $parser->parse($source, $partingBrace);
            return;
        }

        $message = "Unknown kind of parting brace"
            . " on line {$source->line}"
            . " at position {$source->pos}";

        throw new Exception($message);
    }
}

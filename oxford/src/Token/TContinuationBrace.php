<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TContinuationBrace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
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
            $parser->parse($unparsed, $partingBrace);
            return;
        }

        $message = "Unknown kind of parting brace "
            . "on line {$unparsed->line} "
            . "at position {$unparsed->pos} "
            . "in nesting "
            . var_export($parser->listNesting(), true);

        throw new Exception($message);
    }
}

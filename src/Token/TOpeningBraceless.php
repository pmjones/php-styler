<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningBraceless extends AnOpeningStructure
{
    protected const OPENING_BRACE_MAP = [
        TIf::class => TIfOpeningBrace::class,
        TElseif::class => TElseifOpeningBrace::class,
        TElse::class => TElseOpeningBrace::class,
        TFor::class => TForOpeningBrace::class,
        TForeach::class => TForeachOpeningBrace::class,
        TWhile::class => TWhileOpeningBrace::class,
    ];

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $nesting = $parser->getNesting();
        $braceClass = self::OPENING_BRACE_MAP[$nesting] ?? null;

        if ($braceClass !== null) {
            $synthetic = new \PhpToken(
                $source->id,
                '{',
                $source->line,
                $source->pos,
            );

            $parser->add($synthetic, $braceClass);
            $parser->pushNesting($source, self::class);
            $parser->indentIncr();
            return;
        }

        // Fallback for callers (TSwitchClosingParen, TMatchClosingParen)
        // whose nesting isn't in OPENING_BRACE_MAP. These callers only
        // dispatch here for malformed input (switch/match without a `{`
        // or `:`), so the nesting stack is already in an invalid shape —
        // we add a stand-in nesting so a later closer can throw
        // diagnosably rather than silently producing wrong output.
        $synthetic = new \PhpToken($source->id, '', $source->line, $source->pos);
        $parser->addNesting($synthetic, self::class);
        $parser->indentIncr();
    }
}

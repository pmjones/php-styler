<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningBraceless extends AToken implements TOpeningStructure
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

        $synthetic = new \PhpToken($source->id, '', $source->line, $source->pos);
        $parser->addNesting($synthetic, self::class);
        $parser->indentIncr();
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TElse::class);
        $parser->indentDecr();
        $synthetic = new \PhpToken($source->id, '', $source->line, $source->pos);
        $parser->add($synthetic, self::class);
    }
}

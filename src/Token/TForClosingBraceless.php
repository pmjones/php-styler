<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForClosingBraceless extends AToken implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $synthetic = new \PhpToken($source->id, '}', $source->line, $source->pos);
        $parser->parse($synthetic, TForClosingBrace::class);
    }
}

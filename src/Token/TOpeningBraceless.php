<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningBraceless extends T implements TOpeningStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $synthetic = new \PhpToken($source->id, '', $source->line, $source->pos);
        $parser->addNesting($synthetic, self::class);
        $parser->indentIncr();
    }
}

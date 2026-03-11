<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class THaltCompilerSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(THaltCompiler::class);

        $parser->add($source, self::class);
    }
}

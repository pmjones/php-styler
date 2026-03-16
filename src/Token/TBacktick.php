<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TBacktick extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(self::class)) {
            $parser->noSpace();
            $parser->closeNesting($source, self::class, self::class);
            $parser->space();
        } else {
            $parser->addNesting($source, self::class);
        }
    }
}

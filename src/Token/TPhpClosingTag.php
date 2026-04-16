<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPhpClosingTag extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        // closing tag acts as semicolon for short echo tags
        if ($parser->atNesting(TEcho::class)) {
            $parser->parse($source, TEchoEndSemicolon::class);
        }

        $parser->add($source, static::class);
    }
}

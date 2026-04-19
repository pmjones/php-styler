<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPhpClosingTagContinuation extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        // closing tag acts as semicolon for short echo tags
        if ($parser->atNesting(TEcho::class)) {
            $parser->parse($source, TEchoEndSemicolon::class);
        }

        // only remove style-inserted line breaks; keep source-originating ones
        if (! $parser->source->hasPrevEol()) {
            $parser->removeTrailingLineBreaks();
            $parser->space();
        }

        $parser->add($source, static::class);
    }
}

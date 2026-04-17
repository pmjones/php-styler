<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPhpClosingTag extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getNextSource() !== null) {
            $parser->parse($source, TPhpClosingTagContinuation::class);

            return;
        }

        $parser->add($source, static::class);
    }
}

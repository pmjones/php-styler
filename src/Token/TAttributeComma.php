<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TAttributeComma extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // trailing comma before ] — skip silently
        $nextOffset = $parser->source->findNextNonWhitespace();

        if ($nextOffset !== null && $parser->source->getAt($nextOffset)->is(']')) {
            return;
        }

        // expand: close current attribute group, open new one
        $closer = new PhpToken(AToken::SYNTHETIC, ']');
        $opener = new PhpToken(AToken::SYNTHETIC, '#[');

        if ($parser->atNesting(TInlineAttribute::class)) {
            $parser->parse($closer, TInlineAttributeClosingBracket::class);
            $parser->addNesting($opener, TInlineAttribute::class);
        } else {
            $parser->parse($closer, TAttributeClosingBracket::class);
            $parser->addNesting($opener, TAttribute::class);
        }
    }
}

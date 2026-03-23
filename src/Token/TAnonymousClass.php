<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Syntax: new class
 */
class TAnonymousClass extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);

        // remove empty () from anonymous class declarations
        $parenOpenOffset = $parser->findNextNonWhitespaceOffset();

        if ($parenOpenOffset === null) {
            return;
        }

        if (! $parser->getSourceAt($parenOpenOffset)->is('(')) {
            return;
        }

        $parenCloseOffset = $parser->findNextNonWhitespaceOffset(
            $parenOpenOffset + 1,
        );

        if ($parenCloseOffset === null) {
            return;
        }

        if (! $parser->getSourceAt($parenCloseOffset)->is(')')) {
            return;
        }

        // splice out ( [whitespace] ) from source
        $parser->spliceSource(
            $parenOpenOffset,
            $parenCloseOffset - $parenOpenOffset + 1,
            [],
        );
    }
}

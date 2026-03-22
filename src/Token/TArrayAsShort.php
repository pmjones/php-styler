<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayAsShort extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // Find the next non-whitespace source token after 'array'
        $openOffset = $parser->findNextNonWhitespaceOffset();

        // If not '(', this is a type hint — fall through to normal TArray
        if ($parser->getSourceAt($openOffset)->text !== '(') {
            $parser->add($source, TArray::class);
            return;
        }

        // Find the matching ')'
        $closeOffset = $parser->findMatchingCloseParenOffset($openOffset);

        // Replace '(' with '[' and ')' with ']'
        $open = $parser->getSourceAt($openOffset);
        $parser->setSourceAt(
            $openOffset,
            new PhpToken(ord('['), '[', $open->line, $open->pos),
        );

        $close = $parser->getSourceAt($closeOffset);
        $parser->setSourceAt(
            $closeOffset,
            new PhpToken(ord(']'), ']', $close->line, $close->pos),
        );

        // Don't emit any token for 'array' keyword
    }
}

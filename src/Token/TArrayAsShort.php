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
        $openOffset = $parser->source->findNextNonWhitespace();

        // @codeCoverageIgnoreStart
        // defensive: 'array' token is always followed by more tokens in
        // valid PHP; EOF right after 'array' is not accepted input
        if ($openOffset === null) {
            $parser->add($source, TArray::class);
            return;
        }

        // @codeCoverageIgnoreEnd

        // If not '(', this is a type hint — fall through to normal TArray
        if ($parser->source->getAt($openOffset)->text !== '(') {
            $parser->add($source, TArray::class);
            return;
        }

        // Find the matching ')'
        $closeOffset = $parser->source->matchingCloseParen($openOffset);

        // @codeCoverageIgnoreStart
        // defensive: matching close paren is guaranteed in valid PHP
        if ($closeOffset === null) {
            return;
        }

        // @codeCoverageIgnoreEnd

        // Replace '(' with '[' and ')' with ']'
        $open = $parser->source->getAt($openOffset);

        $parser->source
            ->replaceAt(
                $openOffset,
                new PhpToken(ord('['), '[', $open->line, $open->pos),
            );

        $close = $parser->source->getAt($closeOffset);

        $parser->source
            ->replaceAt(
                $closeOffset,
                new PhpToken(ord(']'), ']', $close->line, $close->pos),
            );

        // Don't emit any token for 'array' keyword
    }
}

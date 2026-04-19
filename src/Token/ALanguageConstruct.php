<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

abstract class ALanguageConstruct extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        parent::parse($parser, $source);
    }

    protected static function tryRemoveParens(Parser $parser) : void
    {
        $openOffset = $parser->source->findNextNonWhitespace();

        if (
            $openOffset === null
            || $parser->source->getAt($openOffset)->text !== '('
        ) {
            return;
        }

        $closeOffset = $parser->source->matchingCloseParen($openOffset);

        if ($closeOffset === null) {
            return;
        }

        $afterClose = $parser->source->findNextNonWhitespace($closeOffset + 1);

        if (
            $afterClose === null
            || $parser->source->getAt($afterClose)->text !== ';'
        ) {
            return;
        }

        // Remove parens: replace ( with space and ) with empty whitespace
        $open = $parser->source->getAt($openOffset);

        $parser->source
            ->replaceAt(
                $openOffset,
                new PhpToken(T_WHITESPACE, ' ', $open->line, $open->pos),
            );

        $close = $parser->source->getAt($closeOffset);

        $parser->source
            ->replaceAt(
                $closeOffset,
                new PhpToken(T_WHITESPACE, '', $close->line, $close->pos),
            );
    }
}

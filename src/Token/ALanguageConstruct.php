<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

abstract class ALanguageConstruct extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        self::tryRemoveParens($parser);
        parent::parse($parser, $source);
    }

    protected static function tryRemoveParens(Parser $parser) : void
    {
        $openOffset = $parser->findNextNonWhitespaceOffset();

        if ($openOffset === null || $parser->getSourceAt($openOffset)->text !== '(') {
            return;
        }

        $closeOffset = $parser->findMatchingCloseParenOffset($openOffset);

        if ($closeOffset === null) {
            return;
        }

        $afterClose = $parser->findNextNonWhitespaceOffset($closeOffset + 1);

        if ($afterClose === null || $parser->getSourceAt($afterClose)->text !== ';') {
            return;
        }

        // Remove parens: replace ( with space and ) with empty whitespace
        $open = $parser->getSourceAt($openOffset);
        $parser->setSourceAt(
            $openOffset,
            new PhpToken(T_WHITESPACE, ' ', $open->line, $open->pos),
        );

        $close = $parser->getSourceAt($closeOffset);
        $parser->setSourceAt(
            $closeOffset,
            new PhpToken(T_WHITESPACE, '', $close->line, $close->pos),
        );
    }
}

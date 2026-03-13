<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TAttributeClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $token = $parser->closeNesting($source, self::class, TAttribute::class);
        $parser->space();

        if (
            ! $parser->atNesting(TParamsOpeningParen::class)
            || (
                $token->openingToken instanceof TAttribute
                && $token->openingToken->ownLine
            )
            || $parser->hasNextEol()
        ) {
            $parser->lineBreak();
        } else {
            $parser->addSplit(new TSplitAttribute(T::SYNTHETIC, ''));
        }
    }
}

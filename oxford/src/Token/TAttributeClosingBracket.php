<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TAttributeClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $token = $parser->closeNesting($unparsed, self::class, TAttribute::class);
        $parser->space();

        if (
            ! $parser->atNesting(TParamsOpeningParen::class)
            || ($token->openingToken instanceof TAttribute && $token->openingToken->ownLine)
            || $parser->hasNextEol()
        ) {
            $parser->lineBreak();
        } else {
            $parser->addSplitPoint(TSplittable::ATTRIBUTE);
        }
    }
}

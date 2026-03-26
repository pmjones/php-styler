<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TLogicalAndAsBooleanAnd extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $source = new PhpToken(T_BOOLEAN_AND, '&&', $source->line, $source->pos);
        $parser->add($source, TBooleanAnd::class);

        if ($parser->getStyle(TBooleanAnd::class)->spaceAfter !== false) {
            $parser->space();
        }
    }
}

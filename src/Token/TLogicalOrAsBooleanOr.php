<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TLogicalOrAsBooleanOr extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $source = new PhpToken(T_BOOLEAN_OR, '||', $source->line, $source->pos);
        $parser->add($source, TBooleanOr::class);

        if ($parser->getStyle(TBooleanOr::class)->spaceAfter !== false) {
            $parser->space();
        }
    }
}

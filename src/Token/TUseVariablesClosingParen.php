<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseVariablesClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting(
            $source,
            self::class,
            TUseVariablesOpeningParen::class,
        );

        $parser->popNesting(TUseVariables::class);
    }
}

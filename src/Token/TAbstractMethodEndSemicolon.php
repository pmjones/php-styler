<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TAbstractMethodEndSemicolon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        $parser->popNesting(TFunction::class);

        $parser->add($source, self::class);
    }
}

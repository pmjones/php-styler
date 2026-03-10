<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TAbstractMethodEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        $parser->popNesting(TFunction::class);

        $parser->add($unparsed, self::class);
    }
}

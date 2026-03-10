<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDefaultCase extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);
            $parser->popNesting(TCase::class, TDefaultCase::class, TCaseAfterCase::class, TDefaultAfterCase::class);
            $parser->parse($unparsed, TDefaultAfterCase::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}

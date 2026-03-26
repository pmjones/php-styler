<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDefaultCase extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);
            $parser->popNesting(
                TCase::class,
                TDefaultCase::class,
                TCaseAfterCase::class,
                TDefaultAfterCase::class,
            );
            $parser->parse($source, TDefaultAfterCase::class);
            return;
        }

        $parser->addNesting($source, self::class);
    }
}

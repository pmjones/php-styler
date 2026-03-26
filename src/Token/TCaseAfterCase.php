<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCaseAfterCase extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->indentDecr();
        $parser->addNesting($source, self::class);
    }
}

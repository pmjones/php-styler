<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMemberDoubleColon extends T implements TSplittableFluent
{
    public function splitBefore() : ?TSplit
    {
        return new TSplitStaticFluent(T_WHITESPACE, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->add($source, static::class);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMatchArmComma extends T implements TSplittableComma
{
    public function splitAfter() : ?TSplit
    {
        return new TSplitComma(T_WHITESPACE, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }
}

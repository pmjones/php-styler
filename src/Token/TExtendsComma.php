<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TExtendsComma extends T implements TSplittableComma
{
    public function splitPointAfter() : ?TSplitPoint
    {
        return new TSplitListComma(T_WHITESPACE, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }
}

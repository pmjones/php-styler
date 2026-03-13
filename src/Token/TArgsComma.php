<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArgsComma extends T implements TSplittableComma
{
    public function splitAfter(Parser $parser) : ?TSplit
    {
        return new TSplitComma(T::SYNTHETIC, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }
}

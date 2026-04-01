<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForComma extends AToken
{
    public function splitAfter(Parser $parser) : ?TSplit
    {
        return new TSplitForComma(AToken::SYNTHETIC, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseVariablesComma extends AToken implements ASplittableComma
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }

    public function splitAfter(Parser $parser) : ?TSplit
    {
        return new TSplitComma(AToken::SYNTHETIC, '');
    }
}

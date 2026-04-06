<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TTernaryQuestion extends AToken implements ASplittableOperator
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function splitBefore(Parser $parser) : ?TSplit
    {
        return new TSplitTernary(AToken::SYNTHETIC, '');
    }
}

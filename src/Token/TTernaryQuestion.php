<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TTernaryQuestion extends T implements TSplittableOperator
{
    public function splitCategory() : int
    {
        return self::LOOSE_OPERATOR;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

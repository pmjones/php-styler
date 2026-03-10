<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMemberDoubleColon extends T implements TSplittableFluent
{
    public function splitCategory() : int
    {
        return self::FLUENT;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $parser->add($unparsed, static::class);
    }
}

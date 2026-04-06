<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TAbstractMagicMethodEndSemicolon extends TAbstractMethodEndSemicolon
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        $parser->popNesting(TMagicMethod::class);

        $parser->add($source, self::class);
    }

    public function memberType() : string
    {
        return AMemberClosing::MAGIC_METHOD;
    }
}

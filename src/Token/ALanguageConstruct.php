<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

abstract class ALanguageConstruct extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        parent::parse($parser, $source);
    }
}

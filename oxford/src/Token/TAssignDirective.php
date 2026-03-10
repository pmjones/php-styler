<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Syntax: =
 */
class TAssignDirective extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $parser->add($unparsed, static::class);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDDECLARE
 *
 * Syntax: enddeclare
 *
 * Reference: https://www.php.net/manual/en/control-structures.declare.php declare,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEnddeclare extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TDeclareColon::class);
        $parser->popNesting(TDeclare::class);
        $parser->indentDecr();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDFOR
 *
 * Syntax: endfor
 *
 * Reference: https://www.php.net/manual/en/control-structures.for.php for,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEndfor extends AToken implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TForColon::class);
        $parser->popNesting(TFor::class);
        $parser->indentDecr();
        $parser->add($source, self::class);
    }
}

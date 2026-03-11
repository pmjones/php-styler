<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDWHILE
 *
 * Syntax: endwhile
 *
 * Reference: https://www.php.net/manual/en/control-structures.while.php while,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEndwhile extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TWhileColon::class);
        $parser->popNesting(TWhile::class);
        $parser->indentDecr();
        $parser->add($source, self::class);
        $parser->space();
    }
}

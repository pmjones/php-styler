<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDFOREACH
 *
 * Syntax: endforeach
 *
 * Reference: https://www.php.net/manual/en/control-structures.foreach.php foreach,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEndforeach extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TForeachColon::class);
        $parser->popNesting(TForeach::class);
        $parser->indentDecr();
        $parser->add($source, self::class);
        $parser->space();
    }
}

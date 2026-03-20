<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDIF
 *
 * Syntax: endif
 *
 * Reference: https://www.php.net/manual/en/control-structures.if.php if,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEndif extends AToken implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TIfColon::class, TElseColon::class, TElseifColon::class);
        $parser->popNesting(TIf::class, TElse::class, TElseif::class);
        $parser->indentDecr();
        $parser->add($source, self::class);
    }
}

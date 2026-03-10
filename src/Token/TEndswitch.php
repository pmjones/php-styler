<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENDSWITCH
 *
 * Syntax: endswitch
 *
 * Reference: https://www.php.net/manual/en/control-structures.switch.php switch,
 * https://www.php.net/manual/en/control-structures.alternative-syntax.php alternative syntax
 */
class TEndswitch extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $closesCase = false;

        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);
            $parser->popNesting(TCase::class, TDefaultCase::class, TCaseAfterCase::class, TDefaultAfterCase::class);
            $closesCase = true;
        }

        $parser->popNesting(TSwitchColon::class);
        $parser->popNesting(TSwitch::class);
        $parser->indentDecr();
        $parser->add($unparsed, $closesCase ? TEndswitchAfterCase::class : self::class);
        $parser->space();
    }
}

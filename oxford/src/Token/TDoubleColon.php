<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_DOUBLE_COLON
 *
 * Syntax: ::
 *
 * Reference: see T_PAAMAYIM_NEKUDOTAYIM below
 */
class TDoubleColon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TUseTraitOpeningBrace::class)) {
            $parser->add($unparsed, TUseTraitDoubleColon::class);
            return;
        }

        $parser->add($unparsed, TMemberDoubleColon::class);
    }
}

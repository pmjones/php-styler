<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DOUBLE_COLON
 *
 * Syntax: ::
 *
 * Reference: see T_PAAMAYIM_NEKUDOTAYIM below
 */
class TDoubleColon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TUseTraitOpeningBrace::class)) {
            $parser->add($source, TUseTraitDoubleColon::class);
            return;
        }

        $parser->add($source, TMemberDoubleColon::class);
    }
}

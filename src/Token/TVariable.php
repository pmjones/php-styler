<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_VARIABLE
 *
 * Syntax: $foo
 *
 * Reference: https://www.php.net/manual/en/language.variables.php variables
 */
class TVariable extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->inEncapsedString()) {
            $parser->noSpace();
            $parser->add($source, TEncapsedVariable::class);
            return;
        }

        parent::parse($parser, $source);
    }
}

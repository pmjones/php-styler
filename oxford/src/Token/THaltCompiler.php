<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_HALT_COMPILER
 *
 * Syntax: __halt_compiler()
 *
 * Reference: https://www.php.net/manual/en/function.halt-compiler.php __halt_compiler
 */
class THaltCompiler extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}

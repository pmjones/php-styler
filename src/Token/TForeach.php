<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FOREACH
 *
 * Syntax: foreach
 *
 * Reference: https://www.php.net/manual/en/control-structures.foreach.php for
 */
class TForeach extends AToken
{
    public const OPENING_BRACE = TForeachOpeningBrace::class;

    public const CLOSING_BRACE = TForeachClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

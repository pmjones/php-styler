<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_MATCH
 *
 * Syntax: match
 *
 * Reference: https://www.php.net/manual/en/control-structures.match.php match (available as of PHP 8.0.0)
 */
class TMatch extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}

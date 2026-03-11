<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_AS
 *
 * Syntax: as
 *
 * Reference: https://www.php.net/manual/en/control-structures.foreach.php foreach
 */
class TAs extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parseClass = match ($parser->getNesting()) {
            TForeachOpeningParen::class => TForeachAs::class,

            TUse::class, TUseFunction::class, TUseConst::class => TUseAs::class,

            TUseTraitOpeningBrace::class => TUseTraitAs::class,

            default => self::class,
        };

        $parser->add($unparsed, $parseClass);
    }
}

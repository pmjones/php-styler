<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ELSE
 *
 * Syntax: else
 *
 * Reference: https://www.php.net/manual/en/control-structures.else.php else
 */
class TElse extends AToken
{
    public const OPENING_BRACE = TElseOpeningBrace::class;

    public const CLOSING_BRACE = TElseClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if (
            ! $prev instanceof TIfContinuationBrace
            && ! $prev instanceof TElseifContinuationBrace
            && (
                $parser->atNesting(TIfColon::class)
                || $parser->atNesting(TElseifColon::class)
            )
        ) {
            $parser->popNesting(TIfColon::class, TElseifColon::class);
            $parser->popNesting(TIf::class, TElseif::class);
        }

        if ($parser->getNextSource()?->is(T_IF)) {
            $parser->add($source, self::class);
            return;
        }

        $parser->addNesting($source, self::class);

        if (! $parser->getNextSource()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}

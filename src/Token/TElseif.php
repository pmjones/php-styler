<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ELSEIF
 *
 * Syntax: elseif
 *
 * Reference: https://www.php.net/manual/en/control-structures.elseif.php elseif
 */
class TElseif extends AToken
{
    public const OPENING_BRACE = TElseifOpeningBrace::class;

    public const CLOSING_BRACE = TElseifClosingBrace::class;

    public const CLOSING_BRACELESS = TElseifClosingBraceless::class;

    public const CONTINUATION_BRACELESS = TElseifContinuationBraceless::class;

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

        $parser->addNesting($source, self::class);
    }
}

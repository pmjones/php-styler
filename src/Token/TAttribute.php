<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ATTRIBUTE
 *
 * Syntax: #[
 *
 * Reference: https://www.php.net/manual/en/language.attributes.php attributes (available as of PHP 8.0.0)
 */
class TAttribute extends T
{
    public bool $ownLine = false;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $inParams = $parser->atNesting(TParamsOpeningParen::class);
        $blankLine = $parser->hasPrevBlankLine();
        $ownLine = $inParams && $blankLine;

        if ($ownLine) {
            $parser->lineBreak();
        }

        /** @var TAttribute $token */
        $token = $parser->addNesting($source, self::class);
        $token->ownLine = $ownLine;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CONST
 *
 * Syntax: const
 *
 * Reference: https://www.php.net/const https://www.php.net/manual/en/language.oop5.constants.php
 */
class TConst extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TUse::class)) {
            $parser->popNesting(TUse::class);
            $parser->addNesting($source, TUseConst::class);
            return;
        }

        if ($parser->atClassBody() && ! $parser->hasPrevVisibility()) {
            $parser->add(
                new PhpToken(T_PUBLIC, 'public', $source->line, $source->pos),
                TPublic::class,
            );

            $parser->space();
        }

        $parser->addNesting($source, self::class);
    }
}

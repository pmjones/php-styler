<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ETIT
 *
 * Syntax: exit or die
 *
 * Reference: https://www.php.net/manual/en/function.exit.php exit(), https://www.php.net/manual/en/function.die.php die()
 */
class TExit extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
        $next = $parser->source->peek();

        if ($next !== null && $next->text === '(') {
            if ($parser->getStyle(static::class)->spaceAfter !== false) {
                $parser->space();
            }

            return;
        }

        $synth = new PhpToken(AToken::SYNTHETIC, '(');
        $parser->add($synth, TArgsOpeningParen::class);
        $synth = new PhpToken(AToken::SYNTHETIC, ')');
        $parser->add($synth, TArgsClosingParen::class);

        if ($parser->getStyle(static::class)->spaceAfter !== false) {
            $parser->space();
        }
    }
}

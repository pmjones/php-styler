<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TContinueAsBreak extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! self::isInSwitchBody($parser)) {
            TContinue::parse($parser, $source);
            return;
        }

        // Check for `continue N` where N > 1
        $next = $parser->source->peek();

        if ($next !== null && $next->is(T_LNUMBER) && (int) $next->text > 1) {
            TContinue::parse($parser, $source);
            return;
        }

        // Convert to break
        $parser->add(
            new PhpToken(T_BREAK, 'break', $source->line, $source->pos),
            TBreak::class,
        );

        if ($parser->getStyle(TBreak::class)->spaceAfter !== false) {
            $parser->space();
        }
    }

    private static function isInSwitchBody(Parser $parser) : bool
    {
        $nesting = $parser->listNesting();

        for ($i = count($nesting) - 1; $i >= 0; $i --) {
            $class = $nesting[$i];

            if ($class === TSwitchOpeningBrace::class) {
                return true;
            }

            if (
                $class === TForOpeningBrace::class
                || $class === TForeachOpeningBrace::class
                || $class === TWhileOpeningBrace::class
                || $class === TDoOpeningBrace::class
            ) {
                return false;
            }
        }

        return false;
    }
}

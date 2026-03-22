<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TStringLiteralAsSingleQuote extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($source->text[0] === '"') {
            $inner = substr($source->text, 1, -1);

            if (! str_contains($inner, "'") && ! self::hasUnsafeEscape($inner)) {
                $converted = str_replace('\\"', '"', $inner);
                $newText = "'" . $converted . "'";
                $source = new PhpToken(
                    $source->id,
                    $newText,
                    $source->line,
                    $source->pos,
                );
            }
        }

        $parser->add($source, TStringLiteral::class);

        if ($parser->getStyle(TStringLiteral::class)->spaceAfter !== false) {
            $parser->space();
        }
    }

    private static function hasUnsafeEscape(string $inner) : bool
    {
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i ++) {
            if ($inner[$i] === '\\') {
                if ($i + 1 < $len) {
                    $next = $inner[$i + 1];

                    if ($next !== '\\' && $next !== '"') {
                        return true;
                    }

                    $i ++;
                }
            }
        }

        return false;
    }
}

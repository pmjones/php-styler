<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TCatchClosingBrace;
use PhpStyler\Token\TClosingStructure;
use PhpStyler\Token\TDoContinuationBrace;
use PhpStyler\Token\TElseClosingBrace;
use PhpStyler\Token\TElseifClosingBrace;
use PhpStyler\Token\TFinallyClosingBrace;
use PhpStyler\Token\TForClosingBrace;
use PhpStyler\Token\TForeachClosingBrace;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSemicolon;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSwitchClosingBrace;
use PhpStyler\Token\TWhileClosingBrace;
use PhpStyler\Token\TWhitespaceEol;

class AddBlankLineAfterBlock implements TokenRule
{
    /**
     * @var array<class-string<T>, true>
     */
    private const CLOSING_TOKENS = [
        TIfClosingBrace::class => true,
        TElseClosingBrace::class => true,
        TElseifClosingBrace::class => true,
        TForClosingBrace::class => true,
        TForeachClosingBrace::class => true,
        TWhileClosingBrace::class => true,
        TSwitchClosingBrace::class => true,
        TCatchClosingBrace::class => true,
        TFinallyClosingBrace::class => true,
    ];

    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $doWhileSemicolon = false;

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token instanceof TDoContinuationBrace) {
                $doWhileSemicolon = true;
            }

            $isBlockEnd = isset(self::CLOSING_TOKENS[get_class($token)]);

            if ($doWhileSemicolon && $token instanceof TSemicolon) {
                $isBlockEnd = true;
                $doWhileSemicolon = false;
            }

            if (! $isBlockEnd) {
                $result[] = $token;
                continue;
            }

            $result[] = $token;

            // look forward past whitespace to find next content token
            $j = $i + 1;

            while (
                $j < $count
                && (
                    $tokens[$j] instanceof TSpace
                    || $tokens[$j] instanceof TWhitespaceEol
                    || $tokens[$j] instanceof TLineBreak
                    || $tokens[$j] instanceof TIndentIncrement
                    || $tokens[$j] instanceof TIndentDecrement
                )
            ) {
                $j ++;
            }

            // already has blank line
            if ($j < $count && $tokens[$j] instanceof TBlankLine) {
                continue;
            }

            // next content is a closing structure brace (last statement in block)
            if ($j < $count && $tokens[$j] instanceof TClosingStructure) {
                continue;
            }

            // end of tokens
            if ($j >= $count) {
                continue;
            }

            // copy existing whitespace tokens (including the line break
            // that ends the current line), then insert blank line tokens
            for ($k = $i + 1; $k < $j; $k ++) {
                $result[] = $tokens[$k];
            }

            $result[] = new TBlankLine(T::SYNTHETIC, "\n\n");
            $result[] = new TLineBreak(T::SYNTHETIC, '');

            // advance past the whitespace tokens already copied
            $i = $j - 1;
        }

        return $result;
    }
}

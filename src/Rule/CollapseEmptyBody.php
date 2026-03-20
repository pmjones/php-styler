<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TClasslikeOpeningBrace;
use PhpStyler\Token\TClosingStructure;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;

class CollapseEmptyBody implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! $this->isCollapsibleOpener($token)) {
                $result[] = $token;
                continue;
            }

            // scan forward past whitespace/indent tokens to find the closer
            $closerIndex = null;

            for ($j = $i + 1; $j < $count; $j ++) {
                $next = $tokens[$j];

                if (
                    $next instanceof TLineBreak
                    || $next instanceof TBlankLine
                    || $next instanceof TSpace
                    || $next instanceof TIndentIncrement
                    || $next instanceof TIndentDecrement
                ) {
                    continue;
                }

                // first non-whitespace/indent token
                if ($next instanceof TClosingStructure) {
                    $closerIndex = $j;
                }

                break;
            }

            if ($closerIndex === null) {
                $result[] = $token;
                continue;
            }

            // collapse: replace the TLineBreak before the opening brace
            // with a TSpace (to bring brace to same line)
            $lastIndex = count($result) - 1;

            if ($lastIndex >= 0 && $result[$lastIndex] instanceof TLineBreak) {
                $result[$lastIndex] = new TSpace(
                    T_WHITESPACE,
                    ' ',
                );
            }

            // emit opening brace and closing brace, skip interior tokens
            $result[] = $token;
            $result[] = $tokens[$closerIndex];

            // skip past the closing brace in the source
            $i = $closerIndex;
        }

        return $result;
    }

    private function isCollapsibleOpener(T $token) : bool
    {
        return $token instanceof TFunctionOpeningBrace
            || $token instanceof TClasslikeOpeningBrace
            || $token instanceof TAnonymousOpeningBrace;
    }
}

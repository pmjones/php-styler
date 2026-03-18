<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TOpeningStructure;
use PhpStyler\Token\TPhpOpeningTag;
use PhpStyler\Token\TPhpOpeningTagInline;
use PhpStyler\Token\TReturn;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TWhitespaceEol;

class AddBlankLineBeforeReturn implements TokenRule
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

            if (! ($token instanceof TReturn)) {
                $result[] = $token;
                continue;
            }

            $k = count($result) - 1;

            while (
                $k >= 0
                && (
                    $result[$k] instanceof TSpace
                    || $result[$k] instanceof TWhitespaceEol
                    || $result[$k] instanceof TLineBreak
                    || $result[$k] instanceof TIndentIncrement
                    || $result[$k] instanceof TIndentDecrement
                )
            ) {
                $k --;
            }

            if (
                $k >= 0
                && $result[$k] instanceof TBlankLine
            ) {
                $result[] = $token;
                continue;
            }

            if (
                $k >= 0
                && $result[$k] instanceof TOpeningStructure
            ) {
                $result[] = $token;
                continue;
            }

            if (
                $k >= 0
                && (
                    $result[$k] instanceof TPhpOpeningTag
                    || $result[$k] instanceof TPhpOpeningTagInline
                )
            ) {
                $result[] = $token;
                continue;
            }

            if ($k < 0) {
                $result[] = $token;
                continue;
            }

            $result[] = new TBlankLine(T::SYNTHETIC, "\n\n");
            $result[] = new TLineBreak(T::SYNTHETIC, '');
            $result[] = $token;
        }

        return $result;
    }
}

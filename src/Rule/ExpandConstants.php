<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TConst;
use PhpStyler\Token\TConstComma;
use PhpStyler\Token\TConstEndSemicolon;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TNamespaceConstEndSemicolon;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;

class ExpandConstants implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        /** @var AToken[] $result */
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! ($token instanceof TConstComma)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // found a TConstComma — back-track to find TConst
            $constPos = null;

            for ($b = count($result) - 1; $b >= 0; $b --) {
                if ($result[$b] instanceof TConst) {
                    $constPos = $b;
                    break;
                }
            }

            if ($constPos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            /** @var AToken $constToken */
            $constToken = $result[$constPos];

            // back-track past TConst to collect prefix tokens (modifiers, spaces)
            $prefixStart = $constPos;

            for ($b = $constPos - 1; $b >= 0; $b --) {
                if (
                    $result[$b] instanceof TLineBreak
                    || $result[$b] instanceof TBlankLine
                    || $result[$b] instanceof TIndentIncrement
                    || $result[$b] instanceof TIndentDecrement
                ) {
                    break;
                }

                $prefixStart = $b;
            }

            $prefix = array_slice($result, $prefixStart, $constPos - $prefixStart);

            // find the end semicolon
            $semicolonPos = null;
            $semicolonClass = null;

            for ($j = $i + 1; $j < $count; $j ++) {
                if (
                    $tokens[$j] instanceof TConstEndSemicolon
                    || $tokens[$j] instanceof TNamespaceConstEndSemicolon
                ) {
                    $semicolonPos = $j;
                    $semicolonClass = $tokens[$j]::class;
                    break;
                }
            }

            if ($semicolonPos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // collect first segment from result (after TConst to end of result),
            // skipping only leading TSpace/TSplit
            $segments = [];
            $currentSegment = [];
            $skipLeading = true;

            for ($b = $constPos + 1; $b < count($result); $b ++) {
                $backToken = $result[$b];

                if (
                    $skipLeading
                    && (
                        $backToken instanceof TSpace || $backToken instanceof TSplit
                    )
                ) {
                    continue;
                }

                $skipLeading = false;
                $currentSegment[] = $backToken;
            }

            $segments[] = $currentSegment;

            // trim result back to before the prefix

            /** @var AToken[] $result */
            $result = array_slice($result, 0, $prefixStart);

            // collect remaining segments from tokens (after the comma),
            // skipping only leading TSpace/TSplit per segment
            $currentSegment = [];
            $skipLeading = true;

            for ($j = $i + 1; $j < $semicolonPos; $j ++) {
                $innerToken = $tokens[$j];

                if ($innerToken instanceof TConstComma) {
                    if ($currentSegment !== []) {
                        $segments[] = $currentSegment;
                        $currentSegment = [];
                        $skipLeading = true;
                    }

                    continue;
                }

                if (
                    $skipLeading
                    && (
                        $innerToken instanceof TSpace
                        || $innerToken instanceof TSplit
                    )
                ) {
                    continue;
                }

                $skipLeading = false;
                $currentSegment[] = $innerToken;
            }

            if ($currentSegment !== []) {
                $segments[] = $currentSegment;
            }

            // build expanded statements
            $line = $constToken->line;
            $pos = $constToken->pos;
            $first = true;

            foreach ($segments as $segment) {
                if (! $first) {
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                }

                $first = false;

                // emit prefix (modifiers + spaces)
                foreach ($prefix as $prefixToken) {
                    $result[] = $prefixToken;
                }

                $result[] = new TConst(T_CONST, 'const', $line, $pos);
                $result[] = new TSpace(AToken::SYNTHETIC, ' ', $line, $pos);

                foreach ($segment as $segToken) {
                    $result[] = $segToken;
                }

                $result[] = new $semicolonClass(ord(';'), ';', $line, $pos);
            }

            // skip past the original semicolon
            $i = $semicolonPos + 1;
        }

        /** @var AToken[] $result */
        return $result;
    }
}

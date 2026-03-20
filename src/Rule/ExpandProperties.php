<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TPropertyComma;
use PhpStyler\Token\TPropertyEndSemicolon;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TVariable;

class ExpandProperties implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! ($token instanceof TPropertyComma)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // found a TPropertyComma — back-track to find the first TVariable
            $variablePos = null;

            for ($b = count($result) - 1; $b >= 0; $b --) {
                if ($result[$b] instanceof TVariable) {
                    $variablePos = $b;
                    break;
                }
            }

            if ($variablePos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // back-track past the variable to collect prefix tokens (modifiers + type + spaces)
            $prefixStart = $variablePos;

            for ($b = $variablePos - 1; $b >= 0; $b --) {
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

            $prefix = array_slice($result, $prefixStart, $variablePos - $prefixStart);

            // find the TPropertyEndSemicolon
            $semicolonPos = null;

            for ($j = $i + 1; $j < $count; $j ++) {
                if ($tokens[$j] instanceof TPropertyEndSemicolon) {
                    $semicolonPos = $j;
                    break;
                }
            }

            if ($semicolonPos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // collect first segment from result (from variable to end of result),
            // keeping TSpace for internal spacing
            $segments = [];
            $currentSegment = [];

            for ($b = $variablePos; $b < count($result); $b ++) {
                $backToken = $result[$b];

                if ($backToken instanceof TSplit) {
                    continue;
                }

                $currentSegment[] = $backToken;
            }

            $segments[] = $currentSegment;

            // trim result back to before the prefix
            $result = array_slice($result, 0, $prefixStart);

            // collect remaining segments from tokens (after the comma),
            // skipping only leading TSpace/TSplit per segment
            $currentSegment = [];
            $skipLeading = true;

            for ($j = $i + 1; $j < $semicolonPos; $j ++) {
                $innerToken = $tokens[$j];

                if ($innerToken instanceof TPropertyComma) {
                    if ($currentSegment !== []) {
                        $segments[] = $currentSegment;
                        $currentSegment = [];
                        $skipLeading = true;
                    }

                    continue;
                }

                if (
                    $skipLeading
                    && ($innerToken instanceof TSpace || $innerToken instanceof TSplit)
                ) {
                    continue;
                }

                $skipLeading = false;

                if ($innerToken instanceof TSplit) {
                    continue;
                }

                $currentSegment[] = $innerToken;
            }

            if ($currentSegment !== []) {
                $segments[] = $currentSegment;
            }

            // build expanded statements
            $line = $token->line;
            $pos = $token->pos;
            $first = true;

            foreach ($segments as $segment) {
                if (! $first) {
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                }

                $first = false;

                // emit prefix (modifiers + type + spaces)
                foreach ($prefix as $prefixToken) {
                    $result[] = $prefixToken;
                }

                foreach ($segment as $segToken) {
                    $result[] = $segToken;
                }

                $result[] = new TPropertyEndSemicolon(ord(';'), ';', $line, $pos);
            }

            // skip past the original semicolon
            $i = $semicolonPos + 1;
        }

        return $result;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TUseTrait;
use PhpStyler\Token\TUseTraitComma;
use PhpStyler\Token\TUseTraitEndSemicolon;

class ExpandTraitUse implements TokenRule
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

            if (! ($token instanceof TUseTraitComma)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // found a TUseTraitComma — back-track to find TUseTrait
            $useTraitPos = null;

            for ($b = count($result) - 1; $b >= 0; $b --) {
                if ($result[$b] instanceof TUseTrait) {
                    $useTraitPos = $b;
                    break;
                }
            }

            if ($useTraitPos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            $useTraitToken = $result[$useTraitPos];

            // find the TUseTraitEndSemicolon
            $semicolonPos = null;

            for ($j = $i + 1; $j < $count; $j ++) {
                if ($tokens[$j] instanceof TUseTraitEndSemicolon) {
                    $semicolonPos = $j;
                    break;
                }
            }

            if ($semicolonPos === null) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // collect all segments: everything between TUseTrait and TUseTraitEndSemicolon
            // first segment is from after TUseTrait (in result) to current position
            $segments = [];
            $currentSegment = [];

            // collect first segment from result (after TUseTrait + TSpace)
            for ($b = $useTraitPos + 1; $b < count($result); $b ++) {
                $backToken = $result[$b];

                if ($backToken instanceof TSpace || $backToken instanceof TSplit) {
                    continue;
                }

                $currentSegment[] = $backToken;
            }

            $segments[] = $currentSegment;

            // remove everything from useTraitPos to end of result
            $result = array_slice($result, 0, $useTraitPos);

            // collect remaining segments from tokens (after the comma)
            $currentSegment = [];

            for ($j = $i + 1; $j < $semicolonPos; $j ++) {
                $innerToken = $tokens[$j];

                if ($innerToken instanceof TUseTraitComma) {
                    if ($currentSegment !== []) {
                        $segments[] = $currentSegment;
                        $currentSegment = [];
                    }

                    continue;
                }

                if (
                    $innerToken instanceof TSpace || $innerToken instanceof TSplit
                ) {
                    continue;
                }

                $currentSegment[] = $innerToken;
            }

            if ($currentSegment !== []) {
                $segments[] = $currentSegment;
            }

            // build expanded statements
            $line = $useTraitToken->line;
            $pos = $useTraitToken->pos;
            $first = true;

            foreach ($segments as $segment) {
                if (! $first) {
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                }

                $first = false;

                $result[] = new TUseTrait(T_USE, 'use', $line, $pos);
                $result[] = new TSpace(AToken::SYNTHETIC, ' ', $line, $pos);

                foreach ($segment as $segToken) {
                    $result[] = $segToken;
                }

                $result[] = new TUseTraitEndSemicolon(ord(';'), ';', $line, $pos);
            }

            // skip past the original semicolon
            $i = $semicolonPos + 1;
        }

        return $result;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TPhpClosingTag;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TWhitespace;
use PhpStyler\Token\TWhitespaceEol;

class RemovePhpClosingTag implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        if ($tokens === []) {
            return $tokens;
        }

        // walk backward from end to find last non-whitespace token
        $lastMeaningful = null;

        for ($i = count($tokens) - 1; $i >= 0; $i --) {
            $token = $tokens[$i];

            if (
                $token instanceof TSpace
                || $token instanceof TLineBreak
                || $token instanceof TWhitespace
                || $token instanceof TWhitespaceEol
            ) {
                continue;
            }

            $lastMeaningful = $i;
            break;
        }

        if ($lastMeaningful === null) {
            return $tokens;
        }

        if (! ($tokens[$lastMeaningful] instanceof TPhpClosingTag)) {
            return $tokens;
        }

        // remove the closing tag and any trailing whitespace after it
        return array_slice($tokens, 0, $lastMeaningful);
    }
}

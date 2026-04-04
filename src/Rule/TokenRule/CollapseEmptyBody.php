<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AClosingStructure;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TClasslikeOpeningBrace;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;

class CollapseEmptyBody extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
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

            $closerIndex = $this->findEmptyBodyCloser($tokens, $i + 1, $count);

            if ($closerIndex === null) {
                $result[] = $token;
                continue;
            }

            $this->replaceTrailingLineBreakWithSpace($result);
            $result[] = $token;
            $result[] = $tokens[$closerIndex];
            $i = $closerIndex;
        }

        return $result;
    }

    private function isCollapsibleOpener(AToken $token) : bool
    {
        return $token instanceof TFunctionOpeningBrace
            || $token instanceof TClasslikeOpeningBrace
            || $token instanceof TAnonymousOpeningBrace;
    }

    /**
     * Find the closing brace index if the body is empty (only ignorable
     * tokens between opener and closer). Returns null if body is non-empty.
     *
     * @param AToken[] $tokens
     */
    private function findEmptyBodyCloser(
        array $tokens,
        int $from,
        int $count,
    ) : ?int
    {
        $idx = $this->findNextContent($tokens, $from, $count);

        if ($idx !== null && $tokens[$idx] instanceof AClosingStructure) {
            return $idx;
        }

        return null;
    }

    /**
     * Replace the last TLineBreak in result with a TSpace, bringing
     * the opening brace onto the same line as the declaration.
     *
     * @param AToken[] $result
     */
    private function replaceTrailingLineBreakWithSpace(array &$result) : void
    {
        $lastIndex = count($result) - 1;

        if ($lastIndex >= 0 && $result[$lastIndex] instanceof TLineBreak) {
            $result[$lastIndex] = new TSpace(T_WHITESPACE, ' ');
        }
    }
}

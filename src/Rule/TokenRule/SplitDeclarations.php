<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;

abstract class SplitDeclarations extends ATokenRule
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

            if (! $this->isComma($token)) {
                $result[] = $token;
                continue;
            }

            $prefix = $this->collectPrefix($result);

            if ($prefix === null) {
                $result[] = $token;
                continue;
            }

            $this->emitSplit($result, $tokens, $i, $count, $prefix);

            // skip TSpace/TSplit after the comma to avoid double spacing
            while (
                $i + 1 < $count
                && (
                    $tokens[$i + 1] instanceof TSpace
                    || $tokens[$i + 1] instanceof TSplit
                )
            ) {
                $i ++;
            }
        }

        return $result;
    }

    abstract protected function isComma(AToken $token) : bool;

    /**
     * Find the marker token scanning backward, then collect prefix
     * tokens before it. Returns null if marker not found.
     *
     * @param AToken[] $result
     * @return AToken[]|null
     */
    protected function collectPrefix(array $result) : ?array
    {
        $markerIdx = null;

        for ($j = count($result) - 1; $j >= 0; $j --) {
            if ($this->isMarker($result[$j])) {
                $markerIdx = $j;
                break;
            }
        }

        if ($markerIdx === null) {
            return null;
        }

        $prefix = [];

        for ($j = $markerIdx - 1; $j >= 0; $j --) {
            $t = $result[$j];

            if ($t instanceof TSpace || $t instanceof TSplit) {
                continue;
            }

            if ($this->isPrefixToken($t)) {
                $prefix[] = $t;
                continue;
            }

            break;
        }

        return array_reverse($prefix);
    }

    /**
     * The token type that anchors the backward scan (e.g., TVariable
     * for properties, TConst for constants).
     */
    abstract protected function isMarker(AToken $token) : bool;

    /**
     * Whether a token should be included in the prefix (modifiers, types).
     */
    abstract protected function isPrefixToken(AToken $token) : bool;

    /**
     * Emit the semicolon, line break, and prefix tokens for the split.
     *
     * @param AToken[] $result
     * @param AToken[] $tokens
     * @param AToken[] $prefix
     */
    abstract protected function emitSplit(
        array &$result,
        array $tokens,
        int $i,
        int $count,
        array $prefix,
    ) : void;
}

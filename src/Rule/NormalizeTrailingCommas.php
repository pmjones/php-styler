<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Line;
use PhpStyler\Token\T;
use PhpStyler\Token\TCommentary;
use PhpStyler\Token\TCommaSeparated;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TSplittableComma;

class NormalizeTrailingCommas implements LineRule
{
    /** @param Line[] $lines @return Line[] */
    public function apply(array $lines) : array
    {
        $tokenLineMap = $this->buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                if (! $token instanceof TCommaSeparated) {
                    continue;
                }

                $commaClass = $token->commaClass();

                $closerLineIndex = $tokenLineMap[spl_object_id($token->closingToken)]
                    ?? null;

                if ($closerLineIndex === null) {
                    continue;
                }

                if ($closerLineIndex !== $lineIndex) {
                    $this->ensureTrailingComma($lines, $closerLineIndex, $commaClass);
                } else {
                    $this->removeTrailingComma($lines, $lineIndex, $token);
                }
            }
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @return array<int, int>
     */
    private function buildTokenLineMap(array $lines) : array
    {
        $map = [];

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                $map[spl_object_id($token)] = $lineIndex;
            }
        }

        return $map;
    }

    /**
     * @param Line[] $lines
     * @param class-string<TSplittableComma&T> $commaClass
     */
    private function ensureTrailingComma(
        array &$lines,
        int $closerLineIndex,
        string $commaClass,
    ) : void
    {
        // Find the last-item line (skip blank lines going backward)
        $lastItemLineIndex = $closerLineIndex - 1;

        while ($lastItemLineIndex >= 0 && $lines[$lastItemLineIndex]->isBlank()) {
            $lastItemLineIndex --;
        }

        if ($lastItemLineIndex < 0) {
            return;
        }

        $tokens = $lines[$lastItemLineIndex]->getTokens();
        $count = count($tokens);

        // Walk backward, skipping TSpace and TSplit, to find last content token
        $lastContentPos = $count - 1;

        while (
            $lastContentPos >= 0
            && (
                $tokens[$lastContentPos] instanceof TSpace
                || $tokens[$lastContentPos] instanceof TSplit
            )
        ) {
            $lastContentPos --;
        }

        if ($lastContentPos < 0) {
            return;
        }

        // Already has trailing comma
        if ($tokens[$lastContentPos] instanceof TSplittableComma) {
            return;
        }

        // Don't add comma after an opener (e.g., empty expanded brackets)
        if ($tokens[$lastContentPos]->isOpener()) {
            return;
        }

        // Walk backward further, skipping inline comments and their preceding TSpace
        $insertAfterPos = $lastContentPos;

        while (
            $insertAfterPos >= 0 && $tokens[$insertAfterPos] instanceof TCommentary
        ) {
            $insertAfterPos --;

            // Skip TSpace and TSplit before the comment
            while (
                $insertAfterPos >= 0
                && (
                    $tokens[$insertAfterPos] instanceof TSpace
                    || $tokens[$insertAfterPos] instanceof TSplit
                )
            ) {
                $insertAfterPos --;
            }
        }

        if ($insertAfterPos < 0) {
            return;
        }

        // If it's already a comma at this position, return
        if ($tokens[$insertAfterPos] instanceof TSplittableComma) {
            return;
        }

        // Insert comma after the last-value position
        $comma = new $commaClass(ord(','), ',');
        array_splice($tokens, $insertAfterPos + 1, 0, [$comma]);

        $line = $lines[$lastItemLineIndex];
        $lines[$lastItemLineIndex] = new Line(
            $tokens,
            $line->indent,
            $line->indentStr,
            $line->indentLen,
        );
    }

    /**
     * @param Line[] $lines
     */
    private function removeTrailingComma(
        array &$lines,
        int $lineIndex,
        T $opener,
    ) : void
    {
        if ($opener->closingToken === null) {
            return;
        }

        $line = $lines[$lineIndex];
        $closerPos = $line->findTokenIndex($opener->closingToken);

        if ($closerPos === null) {
            return;
        }

        $tokens = $line->getTokens();

        // Walk backward from closer, skipping TSpace and TSplit
        $pos = $closerPos - 1;

        while (
            $pos >= 0
            && ($tokens[$pos] instanceof TSpace || $tokens[$pos] instanceof TSplit)
        ) {
            $pos --;
        }

        if ($pos < 0 || ! $tokens[$pos] instanceof TSplittableComma) {
            return;
        }

        // Remove the comma (and any TSpace/TSplit between comma and closer)
        array_splice($tokens, $pos, $closerPos - $pos);

        $lines[$lineIndex] = new Line(
            $tokens,
            $line->indent,
            $line->indentStr,
            $line->indentLen,
        );
    }
}

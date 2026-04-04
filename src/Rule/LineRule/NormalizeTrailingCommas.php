<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Token\ACommaListOpener;
use PhpStyler\Token\AComment;
use PhpStyler\Token\ASplittableComma;
use PhpStyler\Token\AToken;

class NormalizeTrailingCommas extends ALineRule
{
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function apply(array $lines) : array
    {
        $tokenLineMap = Line::buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener() || ! $token instanceof ACommaListOpener) {
                    continue;
                }

                $closerLineIndex = $tokenLineMap[
                        $token->closingToken->splObjectId()
                    ]
                    ?? null;

                if ($closerLineIndex === null) {
                    continue;
                }

                if ($closerLineIndex !== $lineIndex) {
                    $this->ensureTrailingComma(
                        $lines,
                        $closerLineIndex,
                        $token->commaClass(),
                    );
                } else {
                    $this->removeTrailingComma($lines, $lineIndex, $token);
                }
            }
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @param class-string<ASplittableComma&AToken> $commaClass
     */
    private function ensureTrailingComma(
        array &$lines,
        int $closerLineIndex,
        string $commaClass,
    ) : void
    {
        $lastItemLineIndex = $this->findLastItemLine($lines, $closerLineIndex);

        if ($lastItemLineIndex === null) {
            return;
        }

        $tokens = $lines[$lastItemLineIndex]->getTokens();
        $insertPos = $this->findCommaInsertPosition($tokens);

        if ($insertPos === null) {
            return;
        }

        // already has trailing comma
        if ($tokens[$insertPos] instanceof ASplittableComma) {
            return;
        }

        // don't add comma after an opener (empty expanded brackets)
        if ($tokens[$insertPos]->isOpener()) {
            return;
        }

        $comma = new $commaClass(ord(','), ',');
        array_splice($tokens, $insertPos + 1, 0, [$comma]);

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
        AToken $opener,
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
        $commaPos = $this->findPrevContent(array_slice($tokens, 0, $closerPos));

        if (
            $commaPos === null || ! $tokens[$commaPos] instanceof ASplittableComma
        ) {
            return;
        }

        array_splice($tokens, $commaPos, $closerPos - $commaPos);

        $lines[$lineIndex] = new Line(
            $tokens,
            $line->indent,
            $line->indentStr,
            $line->indentLen,
        );
    }

    /**
     * Find the line index of the last item before the closer,
     * skipping blank lines.
     *
     * @param Line[] $lines
     */
    private function findLastItemLine(array $lines, int $closerLineIndex) : ?int
    {
        $idx = $closerLineIndex - 1;

        while ($idx >= 0 && $lines[$idx]->isBlank()) {
            $idx --;
        }

        return $idx >= 0 ? $idx : null;
    }

    /**
     * Find the position after which to insert a trailing comma,
     * skipping backward past inline comments.
     *
     * @param AToken[] $tokens
     */
    private function findCommaInsertPosition(array $tokens) : ?int
    {
        $pos = $this->findPrevContent($tokens);

        if ($pos === null) {
            return null;
        }

        // skip past inline comments (and their preceding content)
        while ($pos >= 0 && $tokens[$pos] instanceof AComment) {
            $pos = $this->findPrevContent(array_slice($tokens, 0, $pos));

            if ($pos === null) {
                return null;
            }
        }

        return $pos;
    }
}

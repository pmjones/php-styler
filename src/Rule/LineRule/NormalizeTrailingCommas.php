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
        $lastItemLineIndex = $closerLineIndex - 1;

        while ($lastItemLineIndex >= 0 && $lines[$lastItemLineIndex]->isBlank()) {
            $lastItemLineIndex --;
        }

        if ($lastItemLineIndex < 0) {
            return;
        }

        $tokens = $lines[$lastItemLineIndex]->getTokens();
        $insertPos = $this->findLastNonComment($tokens);

        if (
            $insertPos === null
            || $tokens[$insertPos] instanceof ASplittableComma
            || $tokens[$insertPos]->isOpener()
        ) {
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
        $commaPos = null;

        for ($i = $closerPos - 1; $i >= 0; $i --) {
            if (! $tokens[$i]->isIgnorable()) {
                $commaPos = $i;
                break;
            }
        }

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
     * @param AToken[] $tokens
     */
    private function findLastNonComment(array $tokens) : ?int
    {
        for ($i = count($tokens) - 1; $i >= 0; $i --) {
            if (! $tokens[$i]->isIgnorable() && ! $tokens[$i] instanceof AComment) {
                return $i;
            }
        }

        return null;
    }
}

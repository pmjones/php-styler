<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AComment;
use PhpStyler\Token\ASplittable;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TSplit;

class Splitter
{
    public function __construct(
        private LineFactory $lineFactory = new LineFactory(),
    ) {
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function split(array $lines) : array
    {
        $lines = $this->detectExpansiveAnnotations($lines);
        $lines = $this->normalizeIndents($lines);
        $result = [];

        foreach ($lines as $line) {
            $splitLines = $this->splitLine($line);
            $isSplit = count($splitLines) > 1;

            if ($isSplit) {
                // blank line between adjacent split groups
                $prev = $result !== [] ? end($result) : null;

                if (
                    $prev !== null
                    && $this->shouldInsertBlankLine($prev, $splitLines[0])
                ) {
                    $result[] = new Line(
                        [new TBlankLine(AToken::SYNTHETIC, "\n\n")],
                    );
                }

                foreach ($splitLines as $sl) {
                    $sl->wasSplit = true;
                }
            }

            foreach ($splitLines as $splitLine) {
                $result[] = $splitLine;
            }
        }

        while (true) {
            $tokenLineMap = Line::buildTokenLineMap($result);

            $expanded = $this->findAndApplyOpenerCloserSplit(
                $result,
                $tokenLineMap,
            );

            if ($expanded === null) {
                break;
            }

            $result = $expanded;
        }

        $result = $this->normalizeIndents($result);
        $result = $this->expandCommas($result);
        $result = $this->insertBlankLinesAroundSplits($result);
        return $result;
    }

    /**
     * @return Line[]
     */
    private function splitLine(Line $line) : array
    {
        $lines = [$line];

        if ($line->hasEchoTag()) {
            return $lines;
        }

        if (! $line->forceExpand && $line->hasInteriorComment()) {
            $line->forceExpand = true;
        }

        if (
            ! $line->forceExpand
            && (
                $this->lineFactory->lineLen === 0
                || $line->length() <= $this->lineFactory->lineLen
            )
        ) {
            return $lines;
        }

        $split = $this->trySplit($line);

        if ($split === null) {
            $split = $this->splitAtInteriorLineComments($line);
        }

        if ($split === null) {
            return $lines;
        }

        $lines = [];

        foreach ($split as $splitLine) {
            if ($line->forceExpand) {
                $splitLine->forceExpand = true;
            }

            $lines = array_merge($lines, $this->splitLine($splitLine));
        }

        return $lines;
    }

    /**
     * @return ?Line[]
     */
    private function trySplit(Line $line) : ?array
    {
        $strategies = [];

        // Token-based split groups (operators, commas, etc.)
        foreach ($line->collectSplitGroups() as $group) {
            $strategies[$group['priority']] = fn ()
                => $this->splitAtPositions(
                    $line,
                    $group['positions'],
                    $group['continuation'],
                );
        }

        // Expansion strategies from opener tokens
        $hasFluent = isset($strategies[ASplittable::FLUENT])
            || $line->hasFluentSplits(minCount: 2);

        foreach ($line->collectExpansionPairs() as $priority => $pair) {
            // Skip bracket expansion when fluent splits exist
            if ($hasFluent && $priority === ASplittable::BRACKET) {
                continue;
            }

            $strategies[$priority] ??= fn () => $this->splitAtParens($line, $pair);
        }

        ksort($strategies);

        foreach ($strategies as $strategy) {
            $split = $strategy();

            if ($split !== null) {
                return $split;
            }
        }

        return null;
    }

    /**
     * @return ?Line[]
     */
    private function splitAtInteriorLineComments(Line $line) : ?array
    {
        $tokens = $line->getTokens();
        $lastIdx = $line->lastContentIndex();
        $positions = [];

        foreach ($tokens as $i => $token) {
            if (
                $token instanceof AComment
                && $i < $lastIdx
                && (
                    str_starts_with($token->text, '//')
                    || str_starts_with($token->text, '#')
                )
            ) {
                $positions[] = $i + 1;
            }
        }

        if ($positions === []) {
            return null;
        }

        return $this->splitAtPositions($line, $positions, false);
    }

    /**
     * @param AToken[] $tokens
     */
    private function positionPastTrailingComment(array $tokens, int $pos) : int
    {
        $peek = $pos;

        while (isset($tokens[$peek]) && ! $tokens[$peek]->isContent()) {
            $peek ++;
        }

        if (isset($tokens[$peek]) && $tokens[$peek] instanceof AComment) {
            return $peek + 1;
        }

        return $pos;
    }

    /**
     * @param AToken[] $tokens
     */
    private function advancePastComma(array $tokens, int $commaPos) : int
    {
        $pos = $commaPos + 1;

        while (isset($tokens[$pos]) && $tokens[$pos] instanceof TSplit) {
            $pos ++;
        }

        $advanced = $this->positionPastTrailingComment($tokens, $pos);

        if ($advanced > $pos) {
            $check = $advanced;

            while (isset($tokens[$check]) && ! $tokens[$check]->isContent()) {
                $check ++;
            }

            if (isset($tokens[$check])) {
                return $advanced;
            }
        }

        return $pos;
    }

    /**
     * @param AToken[] $tokens
     */
    private function hasTopLevelOpener(array $tokens) : bool
    {
        $depth = 0;

        foreach ($tokens as $token) {
            if ($token->isOpener()) {
                if ($depth === 0) {
                    return true;
                }

                $depth ++;
            } elseif ($token->openingToken !== null) {
                $depth --;
            }
        }

        return false;
    }

    /**
     * Split a line before each of the given positions.
     * With continuation=true, first segment keeps original indent, rest get continuation indent.
     * With continuation=false, all segments keep original indent.
     *
     * @param int[] $positions
     * @return ?Line[]
     */
    private function splitAtPositions(
        Line $line,
        array $positions,
        bool $continuation,
    ) : ?array
    {
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $contIndent = $continuation ? $line->continuationIndent() : $indent;
        $lines = [];
        $start = 0;

        foreach ($positions as $pos) {
            if ($pos < $start) {
                continue;
            }

            $end = $this->positionPastTrailingComment($tokens, $pos);
            $segment = array_slice($tokens, $start, $end - $start);

            if ($segment !== []) {
                $segLine = $this->lineFactory
                    ->new($segment, $start === 0 ? $indent : $contIndent);

                if (
                    $start === 0
                    && $continuation
                    && ! $line->isExpanded
                    && $this->lineFactory->lineLen > 0
                    && $segLine->length() > $this->lineFactory->lineLen
                    && $this->hasTopLevelOpener($segment)
                ) {
                    $segLine->extraParenIndent = 1;
                }

                $lines[] = $segLine;
            }

            $start = $end;
        }

        // Remaining tokens
        $segment = array_slice($tokens, $start);

        if ($segment !== []) {
            $segLine = $this->lineFactory->new($segment, $contIndent);

            if ($segLine->contentTokenCount() > 0 || $lines === []) {
                $lines[] = $segLine;
            } else {
                $prev = array_pop($lines);

                $lines[] = $this->lineFactory
                    ->new(array_merge($prev->getTokens(), $segment), $prev->indent);
            }
        }

        return count($lines) > 1 ? $lines : null;
    }

    /**
     * @param ?array{int, int, int} $pair
     * @return ?Line[]
     */
    private function splitAtParens(Line $line, ?array $pair = null) : ?array
    {
        $pair ??= $line->findBestPair();

        if ($pair === null) {
            return null;
        }

        [$openerPos, $closerPos, $argCount] = $pair;
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $extra = $line->extraParenIndent;
        $before = array_slice($tokens, 0, $openerPos + 1);
        $inside = array_slice($tokens, $openerPos + 1, $closerPos - $openerPos - 1);
        $after = array_slice($tokens, $closerPos);

        if ($inside === []) {
            return [
                $this->lineFactory->new($before, $indent),
                $this->lineFactory->new($after, $indent + $extra),
            ];
        }

        $contentLine = $this->lineFactory->new($inside, $indent + 1 + $extra);
        $contentLine->isExpanded = $argCount === 0;

        return [
            $this->lineFactory->new($before, $indent),
            $contentLine,
            $this->lineFactory->new($after, $indent + $extra),
        ];
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function detectExpansiveAnnotations(array $lines) : array
    {
        $markNext = false;

        foreach ($lines as $line) {
            if ($markNext && ! $line->isBlank()) {
                $line->forceExpand = true;
                $markNext = false;
                continue;
            }

            foreach ($line->getTokens() as $token) {
                if (str_contains($token->text, '@php-styler-expansive')) {
                    $markNext = true;
                    break;
                }
            }
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function normalizeIndents(array $lines) : array
    {
        $tokenLineMap = Line::buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            $lastToken = $line->lastContentToken();

            if ($lastToken === null || ! $lastToken->isOpener()) {
                continue;
            }

            $closerId = $lastToken->closingToken->splObjectId();
            $closerLineIndex = $tokenLineMap[$closerId] ?? null;

            if ($closerLineIndex === null || $closerLineIndex <= $lineIndex + 1) {
                continue;
            }

            $expectedIndent = $line->indent + 1;
            $bump = null;
            $closerLine = $lines[$closerLineIndex];
            $closerFirst = $closerLine->firstContentToken();

            $bumpEnd = (
                    $closerFirst !== null
                    && $closerFirst !== $lastToken->closingToken
                )
                ? $closerLineIndex + 1
                : $closerLineIndex;

            for ($i = $lineIndex + 1; $i < $bumpEnd; $i ++) {
                if ($lines[$i]->isBlank()) {
                    continue;
                }

                $bump ??= max(0, $expectedIndent - $lines[$i]->indent);
                $lines[$i]->indent += $bump;
                $lines[$i]->isExpanded = true;
            }
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @param array<int, int> $tokenLineMap
     * @return ?Line[]
     */
    private function findAndApplyOpenerCloserSplit(
        array $lines,
        array $tokenLineMap,
    ) : ?array
    {
        foreach ($lines as $lineIndex => $line) {
            $tokens = $line->getTokens();

            foreach ($tokens as $tokenIndex => $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $closerId = $token->closingToken->splObjectId();
                $closerLineIndex = $tokenLineMap[$closerId] ?? null;

                if ($closerLineIndex === null || $closerLineIndex === $lineIndex) {
                    continue;
                }

                // Split after opener if it's not the last token on its line
                if ($tokenIndex < $line->lastContentIndex()) {
                    return $this->splitAfterOpener(
                        $lines,
                        $lineIndex,
                        $tokenIndex,
                        $closerLineIndex + 1,
                    );
                }

                // Split before closer if it's not the first token on its line
                $closerTokenIndex = $lines[$closerLineIndex]
                    ->findTokenIndex($token->closingToken);

                if ($closerTokenIndex !== null && $closerTokenIndex > 0) {
                    return $this->splitBeforeCloser(
                        $lines,
                        $closerLineIndex,
                        $closerTokenIndex,
                        $line->indent,
                    );
                }
            }
        }

        return null;
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function splitAfterOpener(
        array $lines,
        int $openerLineIndex,
        int $openerTokenIndex,
        int $closerLineIndex,
    ) : array
    {
        $line = $lines[$openerLineIndex];
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $openerToken = $tokens[$openerTokenIndex];

        $before = array_slice($tokens, 0, $openerTokenIndex + 1);
        $after = array_slice($tokens, $openerTokenIndex + 1);

        $lines[$openerLineIndex] = $this->createLine($before, $indent, $line);

        $afterLine = $this->createLine($after, $indent + 1, $line);
        array_splice($lines, $openerLineIndex + 1, 0, [$afterLine]);

        // Bump indent of all content lines between opener and closer
        for ($i = $openerLineIndex + 2; $i < $closerLineIndex; $i ++) {
            $lines[$i]->indent ++;
        }

        // Also bump the closer line if the closer is not its first token
        $firstContent = $lines[$closerLineIndex]->firstContentToken();

        if ($firstContent !== $openerToken->closingToken) {
            $lines[$closerLineIndex]->indent ++;
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function splitBeforeCloser(
        array $lines,
        int $closerLineIndex,
        int $closerTokenIndex,
        int $openerIndent,
    ) : array
    {
        $line = $lines[$closerLineIndex];
        $tokens = $line->getTokens();

        $before = array_slice($tokens, 0, $closerTokenIndex);
        $after = array_slice($tokens, $closerTokenIndex);

        $beforeLine = $this->createLine($before, $line->indent, $line);

        if ($beforeLine->contentTokenCount() === 0 && $closerLineIndex > 0) {
            $prev = $lines[$closerLineIndex - 1];

            $lines[$closerLineIndex - 1] = $this->createLine(
                array_merge($prev->getTokens(), $before),
                $prev->indent,
                $prev,
            );

            $lines[$closerLineIndex] = $this->createLine(
                $after,
                $openerIndent,
                $line,
            );
        } else {
            $lines[$closerLineIndex] = $beforeLine;

            array_splice(
                $lines,
                $closerLineIndex + 1,
                0,
                [$this->createLine($after, $openerIndent, $line)],
            );
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function expandCommas(array $lines) : array
    {
        $tokenLineMap = Line::buildTokenLineMap($lines);

        // Collect all line indices that need comma splitting
        $lineIndices = [];

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $closerId = $token->closingToken->splObjectId();
                $closerLineIndex = $tokenLineMap[$closerId] ?? null;

                if ($closerLineIndex === null || $closerLineIndex <= $lineIndex) {
                    continue;
                }

                $contentIndent = $line->indent + 1;
                $foundIndent = false;

                for ($i = $lineIndex + 1; $i < $closerLineIndex; $i ++) {
                    if ($lines[$i]->isBlank()) {
                        continue;
                    }

                    if (! $foundIndent) {
                        $contentIndent = $lines[$i]->indent;
                        $foundIndent = true;
                    }

                    if ($lines[$i]->findTopLevelComma() !== null) {
                        $lineIndices[$i] = $contentIndent;
                    }
                }
            }
        }

        if ($lineIndices === []) {
            return $lines;
        }

        // Process from bottom to top so insertions don't affect earlier indices
        krsort($lineIndices);

        foreach ($lineIndices as $lineIndex => $openerIndent) {
            $lines = $this->splitLineAtAllCommas(
                $lines,
                $lineIndex,
                $openerIndent,
            );
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function splitLineAtAllCommas(
        array $lines,
        int $lineIndex,
        int $contentIndent,
    ) : array
    {
        $line = $lines[$lineIndex];
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $commaPositions = $line->findTopLevelCommas();

        if ($commaPositions === []) {
            return $lines;
        }

        // Convert comma token positions to split points (after comma + trailing splits/comments)
        $splitPoints = [];

        foreach ($commaPositions as $commaPos) {
            $splitPoints[] = $this->advancePastComma($tokens, $commaPos);
        }

        // Split at all points
        $newLines = [];
        $start = 0;

        foreach ($splitPoints as $splitAt) {
            if ($splitAt <= $start) {
                continue;
            }

            $segment = array_slice($tokens, $start, $splitAt - $start);

            if ($segment !== []) {
                $segIndent = $start === 0 ? $indent : $contentIndent;
                $newLines[] = $this->createLine($segment, $segIndent, $line);
            }

            $start = $splitAt;
        }

        $remaining = array_slice($tokens, $start);

        if ($remaining !== []) {
            $newLines[] = $this->createLine($remaining, $contentIndent, $line);
        }

        if (count($newLines) <= 1) {
            return $lines;
        }

        array_splice($lines, $lineIndex, 1, $newLines);

        return $lines;
    }

    /**
     * @param AToken[] $tokens
     */
    private function createLine(
        array $tokens,
        int $indent,
        ?Line $inheritFrom = null,
    ) : Line
    {
        $line = $this->lineFactory->new($tokens, $indent);

        if ($inheritFrom !== null) {
            $line->wasSplit = $inheritFrom->wasSplit;
        }

        return $line;
    }

    private function shouldInsertBlankLine(Line $above, Line $below) : bool
    {
        return $above->wasSplit !== $below->wasSplit
            && ! $above->isBlank()
            && ! $below->isBlank()
            && $above->lastContentToken()?->style?->blankLineAfter !== false
            && $below->firstContentToken()?->style?->blankLineBefore !== false;
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function insertBlankLinesAroundSplits(array $lines) : array
    {
        for ($i = count($lines) - 1; $i > 0; $i --) {
            $above = $lines[$i - 1];
            $below = $lines[$i];

            if (! $this->shouldInsertBlankLine($above, $below)) {
                continue;
            }

            array_splice(
                $lines,
                $i,
                0,
                [new Line([new TBlankLine(AToken::SYNTHETIC, "\n\n")])],
            );
        }

        foreach ($lines as $line) {
            $line->wasSplit = false;
        }

        return array_values($lines);
    }
}

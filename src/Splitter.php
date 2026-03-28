<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TCommentary;
use PhpStyler\Token\TSpace;
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
            foreach ($this->splitLine($line) as $splitLine) {
                $result[] = $splitLine;
            }
        }

        $result = $this->expandOpenerCloser($result);
        $result = $this->normalizeIndents($result);
        $result = $this->expandCommas($result);
        return $result;
    }

    /**
     * @return Line[]
     */
    private function splitLine(Line $line) : array
    {
        $lines = [$line];

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
        // For condition parens, split at the paren before trying operator splits
        $conditionPair = $line->findConditionPair();

        if ($conditionPair !== null) {
            $split = $this->splitAtParens($line, $conditionPair);

            if ($split !== null) {
                return $split;
            }
        }

        $groups = $line->collectSplitGroups();

        foreach ($groups as $group) {
            $split = $this->splitAtPositions(
                $line,
                $group['positions'],
                $group['continuation'],
            );

            if ($split !== null) {
                return $split;
            }
        }

        return $this->splitAtParens($line);
    }

    /**
     * @param AToken[] $tokens
     */
    private function positionPastTrailingComment(array $tokens, int $pos) : int
    {
        $peek = $pos;

        while (
            isset($tokens[$peek])
            && (
                $tokens[$peek] instanceof TSplit || $tokens[$peek] instanceof TSpace
            )
        ) {
            $peek ++;
        }

        if (isset($tokens[$peek]) && $tokens[$peek] instanceof TCommentary) {
            return $peek + 1;
        }

        return $pos;
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
                $lines[] = $this->lineFactory
                    ->new($segment, $start === 0 ? $indent : $contIndent);
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
        $before = array_slice($tokens, 0, $openerPos + 1);
        $inside = array_slice($tokens, $openerPos + 1, $closerPos - $openerPos - 1);
        $after = array_slice($tokens, $closerPos);
        if ($inside === []) {
            return [
                $this->lineFactory->new($before, $indent),
                $this->lineFactory->new($after, $indent),
            ];
        }

        $contentLine = $this->lineFactory->new($inside, $indent + 1);
        $contentLine->isExpanded = $argCount === 0;

        return [
            $this->lineFactory->new($before, $indent),
            $contentLine,
            $this->lineFactory->new($after, $indent),
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
        $tokenLineMap = $this->buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            $lastToken = $line->lastContentToken();

            if ($lastToken === null || ! $lastToken->isOpener()) {
                continue;
            }

            $closerLineIndex = $tokenLineMap[
                spl_object_id($lastToken->closingToken)
            ]
                ?? null;

            if ($closerLineIndex === null || $closerLineIndex <= $lineIndex + 1) {
                continue;
            }

            $expectedIndent = $line->indent + 1;
            $bump = null;
            $closerLine = $lines[$closerLineIndex];
            $closerFirst = $closerLine->firstContentToken();
            $bumpEnd = (
                $closerFirst !== null && $closerFirst !== $lastToken->closingToken
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
     * @return Line[]
     */
    private function expandOpenerCloser(array $lines) : array
    {
        while (true) {
            $tokenLineMap = $this->buildTokenLineMap($lines);
            $split = $this->findOpenerCloserSplit($lines, $tokenLineMap);

            if ($split === null) {
                return $lines;
            }

            $lines = ($split['type'] === 'opener')
                ? $this->splitAfterOpener($lines, ...$split['args'])
                : $this->splitBeforeCloser($lines, ...$split['args']);
        }
    }

    /**
     * @param Line[] $lines
     * @param array<int, int> $tokenLineMap
     * @return ?array{type: string, args: array{int, int, int}}
     */
    private function findOpenerCloserSplit(
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

                $closerLineIndex = $tokenLineMap[
                    spl_object_id($token->closingToken)
                ]
                    ?? null;

                if ($closerLineIndex === null || $closerLineIndex === $lineIndex) {
                    continue;
                }

                // Split after opener if it's not the last token on its line
                if ($tokenIndex < $line->lastContentIndex()) {
                    return [
                        'type' => 'opener',
                        'args' => [$lineIndex, $tokenIndex, $closerLineIndex + 1],
                    ];
                }

                // Split before closer if it's not the first token on its line
                $closerTokenIndex = $lines[
                    $closerLineIndex
                ]->findTokenIndex($token->closingToken);

                if ($closerTokenIndex !== null && $closerTokenIndex > 0) {
                    return [
                        'type' => 'closer',
                        'args' => [
                            $closerLineIndex,
                            $closerTokenIndex,
                            $line->indent,
                        ],
                    ];
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

        $lines[$openerLineIndex] = $this->lineFactory->new($before, $indent);
        array_splice(
            $lines,
            $openerLineIndex + 1,
            0,
            [$this->lineFactory->new($after, $indent + 1)],
        );

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

        $beforeLine = $this->lineFactory->new($before, $line->indent);

        if ($beforeLine->contentTokenCount() === 0 && $closerLineIndex > 0) {
            $prev = $lines[$closerLineIndex - 1];
            $lines[$closerLineIndex - 1] = $this->lineFactory
                ->new(array_merge($prev->getTokens(), $before), $prev->indent);
            $lines[$closerLineIndex] = $this->lineFactory
                ->new($after, $openerIndent);
        } else {
            $lines[$closerLineIndex] = $beforeLine;
            array_splice(
                $lines,
                $closerLineIndex + 1,
                0,
                [$this->lineFactory->new($after, $openerIndent)],
            );
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function expandCommas(array $lines) : array
    {
        $tokenLineMap = $this->buildTokenLineMap($lines);

        // Collect all line indices that need comma splitting
        $lineIndices = [];

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $closerLineIndex = $tokenLineMap[
                    spl_object_id($token->closingToken)
                ]
                    ?? null;

                if ($closerLineIndex === null || $closerLineIndex <= $lineIndex) {
                    continue;
                }

                for ($i = $lineIndex + 1; $i < $closerLineIndex; $i ++) {
                    if (
                        ! isset($lineIndices[$i])
                        && $lines[$i]->findTopLevelComma() !== null
                    ) {
                        $lineIndices[$i] = true;
                    }
                }
            }
        }

        if ($lineIndices === []) {
            return $lines;
        }

        // Process from bottom to top so insertions don't affect earlier indices
        krsort($lineIndices);

        foreach ($lineIndices as $lineIndex => $_) {
            $lines = $this->splitLineAtAllCommas($lines, $lineIndex);
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function splitLineAtAllCommas(array $lines, int $lineIndex) : array
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
            $splitAt = $commaPos + 1;

            while (isset($tokens[$splitAt]) && $tokens[$splitAt] instanceof TSplit) {
                $splitAt ++;
            }

            $advanced = $this->positionPastTrailingComment($tokens, $splitAt);

            if ($advanced > $splitAt) {
                $check = $advanced;

                while (
                    isset($tokens[$check])
                    && (
                        $tokens[$check] instanceof TSplit
                        || $tokens[$check] instanceof TSpace
                    )
                ) {
                    $check ++;
                }

                if (isset($tokens[$check])) {
                    $splitAt = $advanced;
                }
            }

            $splitPoints[] = $splitAt;
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
                $newLines[] = $this->lineFactory->new($segment, $indent);
            }

            $start = $splitAt;
        }

        $remaining = array_slice($tokens, $start);

        if ($remaining !== []) {
            $newLines[] = $this->lineFactory->new($remaining, $indent);
        }

        if (count($newLines) <= 1) {
            return $lines;
        }

        array_splice($lines, $lineIndex, 1, $newLines);

        return $lines;
    }
}

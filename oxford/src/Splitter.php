<?php
declare(strict_types=1);

namespace Oxford;

use Oxford\Token\T;
use Oxford\Token\TArgsComma;
use Oxford\Token\TArgsOpeningParen;
use Oxford\Token\TArrayComma;
use Oxford\Token\TArrayOpeningBracket;
use Oxford\Token\TArrayConstructOpeningParen;
use Oxford\Token\TCommentHashedInline;
use Oxford\Token\TCommentHashedMidStatement;
use Oxford\Token\TCommentSlashedInline;
use Oxford\Token\TCommentSlashedMidStatement;
use Oxford\Token\TCommentStarredInline;
use Oxford\Token\TParamsComma;
use Oxford\Token\TParamsOpeningParen;
use Oxford\Token\TSpace;
use Oxford\Token\TSplitPoint;
use Oxford\Token\TSplittableComma;
use Oxford\Token\TUseVariablesComma;
use Oxford\Token\TUseVariablesOpeningParen;
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
        $result = $this->normalizeTrailingCommas($result);

        return $this->rejoinOrphans($result);
    }

    /**
     * @return Line[]
     */
    private function splitLine(Line $line) : array
    {
        if ($this->lineFactory->lineLen === null || $line->length() <= $this->lineFactory->lineLen) {
            return [$line];
        }

        $split = $this->trySplit($line);

        if ($split === null) {
            return [$line];
        }

        $result = [];

        foreach ($split as $splitLine) {
            $result = array_merge($result, $this->splitLine($splitLine));
        }

        return $result;
    }

    /** @return Line[]|null */
    private function trySplit(Line $line) : ?array
    {
        $groups = $line->collectSplitGroups();

        foreach ($groups as $group) {
            $split = $this->splitAtPositions($line, $group['positions'], $group['continuation']);

            if ($split !== null) {
                return $split;
            }
        }

        return $this->splitAtParens($line);
    }

    /**
     * Split a line before each of the given positions.
     * With continuation=true, first segment keeps original indent, rest get continuation indent.
     * With continuation=false, all segments keep original indent.
     *
     * @param int[] $positions
     * @return Line[]|null
     */
    private function splitAtPositions(Line $line, array $positions, bool $continuation) : ?array
    {
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $contIndent = $continuation ? $line->continuationIndent() : $indent;
        $lines = [];
        $start = 0;

        foreach ($positions as $pos) {
            $segment = array_slice($tokens, $start, $pos - $start);

            if ($segment !== []) {
                $lines[] = $this->lineFactory->new($segment, $start === 0 ? $indent : $contIndent);
            }

            $start = $pos;
        }

        // Remaining tokens
        $segment = array_slice($tokens, $start);

        if ($segment !== []) {
            $segLine = $this->lineFactory->new($segment, $contIndent);

            if ($segLine->contentTokenCount() > 0 || $lines === []) {
                $lines[] = $segLine;
            } else {
                $prev = array_pop($lines);
                $lines[] = $this->lineFactory->new(array_merge($prev->getTokens(), $segment), $prev->indent);
            }
        }

        return count($lines) > 1 ? $lines : null;
    }

    /** @return Line[]|null */
    private function splitAtParens(Line $line) : ?array
    {
        $pair = $line->findBestPair();

        if ($pair === null) {
            return null;
        }

        [$openerPos, $closerPos, $argCount] = $pair;
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $before = array_slice($tokens, 0, $openerPos + 1);
        $inside = array_slice($tokens, $openerPos + 1, $closerPos - $openerPos - 1);
        $after = array_slice($tokens, $closerPos);
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
    private function normalizeIndents(array $lines) : array
    {
        $tokenLineMap = $this->buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            $lastToken = $line->lastContentToken();

            if ($lastToken === null || ! $lastToken->isOpener()) {
                continue;
            }

            $closerLineIndex = $tokenLineMap[spl_object_id($lastToken->closingToken)] ?? null;

            if ($closerLineIndex === null || $closerLineIndex <= $lineIndex + 1) {
                continue;
            }

            $expectedIndent = $line->indent + 1;
            $bump = null;
            $closerLine = $lines[$closerLineIndex];
            $closerFirst = $closerLine->firstContentToken();
            $bumpEnd = ($closerFirst !== null && $closerFirst !== $lastToken->closingToken)
                ? $closerLineIndex + 1
                : $closerLineIndex;

            for ($i = $lineIndex + 1; $i < $bumpEnd; $i++) {
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
     * @return Line[]
     */
    private function rejoinOrphans(array $lines) : array
    {
        for ($i = 0; $i < count($lines) - 1; $i++) {
            $line = $lines[$i];
            $tokens = $line->getTokens();
            $nextLine = $lines[$i + 1];

            if (
                $line->contentTokenCount() === 1
                && $nextLine->rejoinOrphanBefore()
            ) {
                $merged = array_merge($tokens, [new TSpace(T_WHITESPACE, ' ')], $nextLine->getTokens());
                $lines[$i] = $this->lineFactory->new($merged, $line->indent);
                array_splice($lines, $i + 1, 1);
                continue;
            }
        }

        return array_values($lines);
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
    private function findOpenerCloserSplit(array $lines, array $tokenLineMap) : ?array
    {
        foreach ($lines as $lineIndex => $line) {
            $tokens = $line->getTokens();

            foreach ($tokens as $tokenIndex => $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $closerLineIndex = $tokenLineMap[spl_object_id($token->closingToken)] ?? null;

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
                $closerTokenIndex = $lines[$closerLineIndex]->findTokenIndex($token->closingToken);

                if ($closerTokenIndex !== null && $closerTokenIndex > 0) {
                    return [
                        'type' => 'closer',
                        'args' => [$closerLineIndex, $closerTokenIndex, $line->indent],
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
    ) : array {
        $line = $lines[$openerLineIndex];
        $tokens = $line->getTokens();
        $indent = $line->indent;
        $openerToken = $tokens[$openerTokenIndex];

        $before = array_slice($tokens, 0, $openerTokenIndex + 1);
        $after = array_slice($tokens, $openerTokenIndex + 1);

        $lines[$openerLineIndex] = $this->lineFactory->new($before, $indent);
        array_splice($lines, $openerLineIndex + 1, 0, [$this->lineFactory->new($after, $indent + 1)]);

        // Bump indent of all content lines between opener and closer
        for ($i = $openerLineIndex + 2; $i < $closerLineIndex; $i++) {
            $lines[$i]->indent++;
        }

        // Also bump the closer line if the closer is not its first token
        $firstContent = $lines[$closerLineIndex]->firstContentToken();

        if ($firstContent !== $openerToken->closingToken) {
            $lines[$closerLineIndex]->indent++;
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
    ) : array {
        $line = $lines[$closerLineIndex];
        $tokens = $line->getTokens();

        $before = array_slice($tokens, 0, $closerTokenIndex);
        $after = array_slice($tokens, $closerTokenIndex);

        $lines[$closerLineIndex] = $this->lineFactory->new($before, $line->indent);
        array_splice($lines, $closerLineIndex + 1, 0, [$this->lineFactory->new($after, $openerIndent)]);

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function expandCommas(array $lines) : array
    {
        while (true) {
            $tokenLineMap = $this->buildTokenLineMap($lines);
            $split = $this->findCommaSplit($lines, $tokenLineMap);

            if ($split === null) {
                return $lines;
            }

            $lines = $this->splitAfterComma($lines, ...$split);
        }
    }

    /**
     * @param Line[] $lines
     * @param array<int, int> $tokenLineMap
     * @return ?array{int, int}
     */
    private function findCommaSplit(array $lines, array $tokenLineMap) : ?array
    {
        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $closerLineIndex = $tokenLineMap[spl_object_id($token->closingToken)] ?? null;

                if ($closerLineIndex === null || $closerLineIndex <= $lineIndex) {
                    continue;
                }

                for ($i = $lineIndex + 1; $i < $closerLineIndex; $i++) {
                    $splitPos = $lines[$i]->findTopLevelComma();

                    if ($splitPos !== null) {
                        return [$i, $splitPos];
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function splitAfterComma(array $lines, int $lineIndex, int $commaIndex) : array
    {
        $line = $lines[$lineIndex];
        $tokens = $line->getTokens();
        $indent = $line->indent;

        // Include trailing TSplitPoint with the comma (before the split)
        $splitAt = $commaIndex + 1;

        while (isset($tokens[$splitAt]) && $tokens[$splitAt] instanceof TSplitPoint) {
            $splitAt++;
        }

        $before = array_slice($tokens, 0, $splitAt);
        $after = array_slice($tokens, $splitAt);

        $lines[$lineIndex] = $this->lineFactory->new($before, $indent);

        if ($after !== []) {
            array_splice($lines, $lineIndex + 1, 0, [$this->lineFactory->new($after, $indent)]);
        }

        return array_values($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function normalizeTrailingCommas(array $lines) : array
    {
        $tokenLineMap = $this->buildTokenLineMap($lines);

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                if (! $token->isOpener()) {
                    continue;
                }

                $commaClass = match (true) {
                    $token instanceof TArgsOpeningParen => TArgsComma::class,
                    $token instanceof TParamsOpeningParen => TParamsComma::class,
                    $token instanceof TArrayOpeningBracket, $token instanceof TArrayConstructOpeningParen => TArrayComma::class,
                    $token instanceof TUseVariablesOpeningParen => TUseVariablesComma::class,
                    default => null,
                };

                if ($commaClass === null) {
                    continue;
                }

                $closerLineIndex = $tokenLineMap[spl_object_id($token->closingToken)] ?? null;

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
     * @param class-string<TSplittableComma&T> $commaClass
     */
    private function ensureTrailingComma(array &$lines, int $closerLineIndex, string $commaClass) : void
    {
        // Find the last-item line (skip blank lines going backward)
        $lastItemLineIndex = $closerLineIndex - 1;

        while ($lastItemLineIndex >= 0 && $lines[$lastItemLineIndex]->isBlank()) {
            $lastItemLineIndex--;
        }

        if ($lastItemLineIndex < 0) {
            return;
        }

        $tokens = $lines[$lastItemLineIndex]->getTokens();
        $count = count($tokens);

        // Walk backward, skipping TSpace and TSplitPoint, to find last content token
        $lastContentPos = $count - 1;

        while ($lastContentPos >= 0 && ($tokens[$lastContentPos] instanceof TSpace || $tokens[$lastContentPos] instanceof TSplitPoint)) {
            $lastContentPos--;
        }

        if ($lastContentPos < 0) {
            return;
        }

        // Already has trailing comma
        if ($tokens[$lastContentPos] instanceof TSplittableComma) {
            return;
        }

        // Walk backward further, skipping inline comments and their preceding TSpace
        $insertAfterPos = $lastContentPos;

        while (
            $insertAfterPos >= 0
            && ($tokens[$insertAfterPos] instanceof TCommentSlashedInline
                || $tokens[$insertAfterPos] instanceof TCommentHashedInline
                || $tokens[$insertAfterPos] instanceof TCommentStarredInline
                || $tokens[$insertAfterPos] instanceof TCommentSlashedMidStatement
                || $tokens[$insertAfterPos] instanceof TCommentHashedMidStatement)
        ) {
            $insertAfterPos--;

            // Skip TSpace before the comment
            while ($insertAfterPos >= 0 && $tokens[$insertAfterPos] instanceof TSpace) {
                $insertAfterPos--;
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

        $indent = $lines[$lastItemLineIndex]->indent;
        $lines[$lastItemLineIndex] = $this->lineFactory->new($tokens, $indent);
    }

    /**
     * @param Line[] $lines
     */
    private function removeTrailingComma(array &$lines, int $lineIndex, T $opener) : void
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

        // Walk backward from closer, skipping TSpace and TSplitPoint
        $pos = $closerPos - 1;

        while ($pos >= 0 && ($tokens[$pos] instanceof TSpace || $tokens[$pos] instanceof TSplitPoint)) {
            $pos--;
        }

        if ($pos < 0 || ! $tokens[$pos] instanceof TSplittableComma) {
            return;
        }

        // Remove the comma (and any TSpace/TSplitPoint between comma and closer)
        array_splice($tokens, $pos, $closerPos - $pos);

        $lines[$lineIndex] = $this->lineFactory->new($tokens, $line->indent);
    }
}

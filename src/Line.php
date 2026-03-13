<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\T;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TCommentary;
use PhpStyler\Token\TConditionOpener;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TSplittableComma;

class Line
{
    public bool $isExpanded = false;

    public bool $forceExpand = false;

    /** @param T[] $tokens */
    public function __construct(
        private array $tokens = [],
        public int $indent = 0,
        public readonly string $indentStr = '    ',
        public readonly int $indentLen = 4,
    ) {
        while ($tokens !== [] && $tokens[0] instanceof TSpace) {
            array_shift($tokens);
        }

        while ($tokens !== [] && end($tokens) instanceof TSpace) {
            array_pop($tokens);
        }

        $this->tokens = array_values($tokens);
    }

    public function addToken(T $token) : void
    {
        $this->tokens[] = $token;
    }

    public function hasTokens() : bool
    {
        return $this->tokens !== [];
    }

    /** @return T[] */
    public function getTokens() : array
    {
        return $this->tokens;
    }

    public function rejoinOrphanBefore() : bool
    {
        return $this->firstContentToken()?->rejoinOrphanBefore() ?? false;
    }

    public function continuationIndent() : int
    {
        return $this->isExpanded ? $this->indent : $this->indent + 1;
    }

    /** @return array<int, T> */
    public function getTopLevelTokens() : array
    {
        $tokens = $this->tokens;
        $count = count($tokens);
        $result = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token->isOpener()) {
                $closerPos = $this->findTokenIndex($token->closingToken);

                if ($closerPos !== null) {
                    $i = $closerPos;
                    continue;
                }
            }

            $result[$i] = $token;
        }

        return $result;
    }

    public function findTokenIndex(T $target) : ?int
    {
        foreach ($this->tokens as $i => $token) {
            if ($token === $target) {
                return $i;
            }
        }

        return null;
    }

    public function isBlank() : bool
    {
        return $this->firstContentToken() instanceof TBlankLine;
    }

    public function firstContentToken() : ?T
    {
        foreach ($this->tokens as $token) {
            if (! $token instanceof TSplit && ! $token instanceof TSpace) {
                return $token;
            }
        }

        return null;
    }

    public function lastContentToken() : ?T
    {
        $i = $this->lastContentIndex();
        $token = $this->tokens[$i] ?? null;

        return ($token instanceof TSplit || $token instanceof TSpace) ? null : $token;
    }

    public function lastContentIndex() : int
    {
        for ($i = count($this->tokens) - 1; $i >= 0; $i --) {
            if (
                ! $this->tokens[$i] instanceof TSplit
                && ! $this->tokens[$i] instanceof TSpace
            ) {
                return $i;
            }
        }

        return 0;
    }

    public function contentTokenCount() : int
    {
        $count = 0;

        foreach ($this->tokens as $token) {
            if (! $token instanceof TSplit && ! $token instanceof TSpace) {
                $count ++;
            }
        }

        return $count;
    }

    private function lastTopLevelContentIndex() : int
    {
        $topLevel = $this->getTopLevelTokens();
        $keys = array_keys($topLevel);

        for ($i = count($keys) - 1; $i >= 0; $i --) {
            if (
                ! $topLevel[$keys[$i]] instanceof TSplit
                && ! $topLevel[$keys[$i]] instanceof TSpace
            ) {
                return $keys[$i];
            }
        }

        return $keys[0] ?? 0;
    }

    public function render() : string
    {
        if ($this->isBlank()) {
            return '';
        }

        $indent = str_repeat($this->indentStr, $this->indent);
        $parts = [];
        $skipLeading = true;

        foreach ($this->tokens as $token) {
            if ($skipLeading && ($token->text === '' || $token instanceof TSpace)) {
                continue;
            }

            $skipLeading = false;
            $parts[] = $token->render($this);
        }

        return $indent . implode('', $parts);
    }

    public function length() : int
    {
        if ($this->isBlank()) {
            return 0;
        }

        $contentLen = 0;
        $skipLeading = true;

        foreach ($this->tokens as $token) {
            if ($skipLeading && ($token->text === '' || $token instanceof TSpace)) {
                continue;
            }

            $skipLeading = false;
            $contentLen += strlen($token->text);
        }

        return $this->indent * $this->indentLen + $contentLen;
    }

    public function findTopLevelComma() : ?int
    {
        $topLevel = $this->getTopLevelTokens();
        $lastIndex = $this->lastTopLevelContentIndex();

        foreach ($topLevel as $i => $token) {
            if ($token instanceof TSplittableComma && $i !== $lastIndex) {
                // For actual commas (not semicolons), skip if the only remaining
                // content after the comma is an inline comment
                if (
                    $token->text === ',' && $topLevel[$lastIndex] instanceof TCommentary
                ) {
                    continue;
                }

                return $i;
            }
        }

        return null;
    }

    /** @return array{int, int, int}|null */
    public function findConditionPair() : ?array
    {
        foreach ($this->tokens as $i => $token) {
            if (! $token instanceof TConditionOpener || $token->closingToken === null) {
                continue;
            }

            $closerPos = $this->findTokenIndex($token->closingToken);

            if ($closerPos === null || $closerPos - $i <= 1) {
                continue;
            }

            return [$i, $closerPos, $token->argCount];
        }

        return null;
    }

    /** @return array{int, int, int}|null */
    public function findBestPair() : ?array
    {
        $tokens = $this->tokens;
        $count = count($tokens);

        // Phase 1: Collect all top-level pairs
        $pairs = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! $token->isOpener()) {
                continue;
            }

            $closerPos = $this->findTokenIndex($token->closingToken);

            if ($closerPos === null || (! $this->forceExpand && $closerPos - $i <= 1)) {
                continue;
            }

            $pairs[] = [$i, $closerPos, $token->argCount];
            $i = $closerPos;
        }

        if ($pairs === []) {
            return null;
        }

        // Phase 2: Select best pair — prefer the one with commas if exactly one has them
        $commaIndices = [];

        foreach ($pairs as $idx => $pair) {
            if ($pair[2] > 0) {
                $commaIndices[] = $idx;
            }
        }

        if (count($commaIndices) === 1) {
            return $pairs[$commaIndices[0]];
        }

        return $pairs[0];
    }

    /** @return list<array{positions: int[], continuation: bool}> */
    public function collectSplitGroups() : array
    {
        $tokens = $this->tokens;

        /** @var array<int, array{positions: int[], continuation: bool}> $groups */
        $groups = [];

        foreach ($this->getTopLevelTokens() as $i => $token) {
            if ($i === 0 || ! $token instanceof TSplit) {
                continue;
            }

            $order = $token->splitPriority();
            $groups[$order] ??= [
                'positions' => [],
                'continuation' => $token->continuation(),
            ];
            $groups[$order]['positions'][] = $i;
        }

        foreach ($groups as $priority => &$group) {
            /** @var TSplit $firstSplit */
            $firstSplit = $tokens[$group['positions'][0]];

            if ($firstSplit->shouldSkipFirst(count($group['positions']))) {
                array_shift($group['positions']);
            }

            if ($group['positions'] === []) {
                unset($groups[$priority]);
            }
        }

        unset($group);

        ksort($groups);

        return array_values($groups);
    }
}

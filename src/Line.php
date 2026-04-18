<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\ACommaListOpener;
use PhpStyler\Token\AComment;
use PhpStyler\Token\ASplittable;
use PhpStyler\Token\ASplittableComma;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TInlineHtml;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TSplitFluent;

class Line
{
    /**
     * @param Line[] $lines
     * @return array<int, int>
     */
    public static function buildTokenLineMap(array $lines) : array
    {
        $map = [];

        foreach ($lines as $lineIndex => $line) {
            foreach ($line->getTokens() as $token) {
                $map[$token->splObjectId()] = $lineIndex;
            }
        }

        return $map;
    }

    public bool $isExpanded = false;

    public bool $forceExpand = false;

    public bool $wasSplit = false;

    public int $extraParenIndent = 0;

    private ?int $length = null;

    /** @var ?array<int, AToken> */
    private ?array $topLevelTokens = null;

    /** @var ?array<int, int> spl_object_id => token index */
    private ?array $tokenIndex = null;

    /**
     * @param AToken[] $tokens
     */
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

    public function addToken(AToken $token) : void
    {
        $this->tokens[] = $token;
        $this->length = null;
        $this->topLevelTokens = null;
        $this->tokenIndex = null;
    }

    public function hasTokens() : bool
    {
        return $this->tokens !== [];
    }

    /**
     * @return AToken[]
     */
    public function getTokens() : array
    {
        return $this->tokens;
    }

    public function continuationIndent() : int
    {
        return $this->isExpanded ? $this->indent : $this->indent + 1;
    }

    /**
     * @return array<int, AToken>
     */
    public function getTopLevelTokens() : array
    {
        if ($this->topLevelTokens !== null) {
            return $this->topLevelTokens;
        }

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

        return $this->topLevelTokens = $result;
    }

    public function findTokenIndex(AToken $target) : ?int
    {
        if ($this->tokenIndex === null) {
            $this->tokenIndex = [];

            foreach ($this->tokens as $i => $token) {
                $this->tokenIndex[$token->splObjectId()] = (int) $i;
            }
        }

        return $this->tokenIndex[$target->splObjectId()] ?? null;
    }

    public function isBlank() : bool
    {
        return $this->firstContentToken() instanceof TBlankLine;
    }

    public function firstContentToken() : ?AToken
    {
        foreach ($this->tokens as $token) {
            if ($token->isContent()) {
                return $token;
            }
        }

        return null;
    }

    public function lastContentToken() : ?AToken
    {
        $i = $this->lastContentIndex();
        $token = $this->tokens[$i] ?? null;

        return $token !== null && $token->isContent() ? $token : null;
    }

    public function lastContentIndex() : int
    {
        for ($i = count($this->tokens) - 1; $i >= 0; $i --) {
            if ($this->tokens[$i]->isContent()) {
                return $i;
            }
        }

        return 0;
    }

    public function hasFluentSplits(int $minCount = 1) : bool
    {
        $count = 0;

        foreach ($this->getTopLevelTokens() as $token) {
            if ($token instanceof TSplitFluent) {
                $count ++;

                if ($count >= $minCount) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasEchoTag() : bool
    {
        foreach ($this->tokens as $token) {
            if (
                $token instanceof Token\TPhpEchoOpeningTag
                || $token instanceof Token\TPhpEchoOpeningTagContinuation
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasInteriorComment() : bool
    {
        $lastIdx = $this->lastContentIndex();

        foreach ($this->tokens as $i => $token) {
            if ($token instanceof AComment && $i < $lastIdx) {
                return true;
            }
        }

        return false;
    }

    public function contentTokenCount() : int
    {
        $count = 0;

        foreach ($this->tokens as $token) {
            if ($token->isContent()) {
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
            if ($topLevel[$keys[$i]]->isContent()) {
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
        if ($this->length !== null) {
            return $this->length;
        }

        if ($this->isBlank()) {
            return $this->length = 0;
        }

        $column = $this->indent * $this->indentLen;
        $skipLeading = true;

        foreach ($this->tokens as $token) {
            if ($skipLeading && ($token->text === '' || $token instanceof TSpace)) {
                continue;
            }

            $skipLeading = false;

            if ($token instanceof TInlineHtml) {
                // advance column to position after last newline in HTML
                $lastNewline = strrpos($token->text, "\n");

                if ($lastNewline !== false) {
                    $column = strlen($token->text) - $lastNewline - 1;
                } else {
                    $column += strlen($token->text);
                }
            } else {
                $column += strlen($token->text);
            }
        }

        return $this->length = $column;
    }

    public function findTopLevelComma() : ?int
    {
        $commas = $this->findTopLevelCommas();
        return $commas !== [] ? $commas[0] : null;
    }

    /**
     * @return int[]
     */
    public function findTopLevelCommas() : array
    {
        $topLevel = $this->getTopLevelTokens();
        $lastIndex = $this->lastTopLevelContentIndex();
        $result = [];

        foreach ($topLevel as $i => $token) {
            if ($token instanceof ASplittableComma && $i !== $lastIndex) {
                // For actual commas (not semicolons), skip if the only remaining
                // content after the comma is an inline comment
                if (
                    $token->text === ','
                    && $topLevel[$lastIndex] instanceof AComment
                ) {
                    continue;
                }

                $result[] = $i;
            }
        }

        return $result;
    }

    /**
     * @param ?callable(AToken): bool $filter
     * @return ?array{int, int, int}
     */
    public function findBestPair(?callable $filter = null) : ?array
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

            if ($filter !== null && ! $filter($token)) {
                $i = $this->findTokenIndex($token->closingToken) ?? $i;
                continue;
            }

            $closerPos = $this->findTokenIndex($token->closingToken);

            if (
                $closerPos === null
                || (! $this->forceExpand && $closerPos - $i <= 1)
            ) {
                continue;
            }

            $argCount = $token instanceof ACommaListOpener ? $token->argCount : 0;
            $pairs[] = [$i, $closerPos, $argCount];
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

    /**
     * @return array<int, array{int, int, int}>
     */
    public function collectExpansionPairs() : array
    {
        $tokens = $this->tokens;
        $count = count($tokens);
        $result = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! $token->isOpener()) {
                continue;
            }

            $priority = $token->expandPriority() ?? ASplittable::OTHER_PAREN;

            $closerPos = $this->findTokenIndex($token->closingToken);

            if (
                $closerPos === null
                || (! $this->forceExpand && $closerPos - $i <= 1)
            ) {
                continue;
            }

            $argCount = $token instanceof ACommaListOpener ? $token->argCount : 0;
            $result[$priority] ??= [$i, $closerPos, $argCount];
            $i = $closerPos;
        }

        return $result;
    }

    /**
     * @return list<array{priority: int, positions: int[], continuation: bool}>
     */
    public function collectSplitGroups() : array
    {
        $tokens = $this->tokens;

        /** @var array<int, array{priority: int, positions: int[], continuation: bool}> $groups */
        $groups = [];

        foreach ($this->getTopLevelTokens() as $i => $token) {
            if ($i === 0 || ! $token instanceof TSplit) {
                continue;
            }

            $order = $token->splitPriority();

            $groups[$order] ??= [
                'priority' => $order,
                'positions' => [],
                'continuation' => $token->continuation(),
            ];

            $groups[$order]['positions'][] = $i;
        }

        foreach ($groups as $priority => &$group) {
            $filtered = [];

            foreach ($group['positions'] as $pos) {
                $split = $tokens[$pos];

                if ($split instanceof TSplitFluent && $split->chainPosition === 0) {
                    continue;
                }

                $filtered[] = $pos;
            }

            $group['positions'] = $filtered;

            if ($group['positions'] === []) {
                unset($groups[$priority]);
            }
        }

        unset($group);

        ksort($groups);

        return array_values($groups);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\ADocblock;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TConstName;
use PhpStyler\Token\TFunctionCallName;
use PhpStyler\Token\TFunctionCallQualified;
use PhpStyler\Token\TFunctionName;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TUnknownString;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseAlias;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;
use PhpStyler\Token\TWhitespaceEol;

class NormalizeImports extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);
        $result = [];
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isUseKeyword($token)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            [$block, $i] = $this->collectBlock($tokens, $i, $count);

            $remainingTokens = array_slice($tokens, $i);
            $usedNames = $this->collectUsedNames($remainingTokens);
            $this->emitBlock($block, $usedNames, $result);
        }

        return $result;
    }

    /**
     * Collect a contiguous block of use statements starting at $i.
     * Returns [block, newOffset].
     *
     * @param AToken[] $tokens
     * @return array{0: AToken[][], 1: int}
     */
    private function collectBlock(array $tokens, int $i, int $count) : array
    {
        $block = [];
        $currentStatement = [$tokens[$i]];
        $i ++;

        while ($i < $count) {
            $token = $tokens[$i];

            if ($token instanceof TUseEndSemicolon) {
                $currentStatement[] = $token;
                $block[] = $currentStatement;
                $currentStatement = [];
                $i ++;

                // consume separator tokens between statements
                while ($i < $count && $this->isSeparator($tokens[$i])) {
                    $i ++;
                }

                // continue block if next token is another use statement
                if ($i < $count && $this->isUseKeyword($tokens[$i])) {
                    $currentStatement = [$tokens[$i]];
                    $i ++;
                    continue;
                }

                break;
            }

            $currentStatement[] = $token;
            $i ++;
        }

        // handle edge case: unclosed statement at end of tokens
        if ($currentStatement !== []) {
            $block[] = $currentStatement;
        }

        return [$block, $i];
    }

    /**
     * @param AToken[] $remainingTokens
     * @return array<string, true>
     */
    private function collectUsedNames(array $remainingTokens) : array
    {
        $usedNames = [];

        foreach ($remainingTokens as $token) {
            if (
                $token instanceof TUnqualifiedName
                || $token instanceof TFunctionCallName
                || $token instanceof TConstName
                || $token instanceof TUnknownString
            ) {
                $usedNames[$token->text] = true;
            }

            if (
                $token instanceof TQualifiedName
                || $token instanceof TFunctionCallQualified
            ) {
                $parts = explode('\\', $token->text);
                $usedNames[$parts[0]] = true;
            }

            if ($token instanceof ADocblock) {
                $this->extractDocblockNames($token->getDocblock(), $usedNames);
            }
        }

        return $usedNames;
    }

    /**
     * Filter, sort, and emit a use-statement block.
     *
     * @param AToken[][] $block
     * @param array<string, true> $usedNames
     * @param AToken[] $result
     */
    private function emitBlock(
        array $block,
        array $usedNames,
        array &$result,
    ) : void
    {
        $statements = $this->filterAndClassify($block, $usedNames);

        if ($statements === []) {
            return;
        }

        usort(
            $statements,
            fn (array $a, array $b) : int
                => $a['kind'] <=> $b['kind']
                    ?: strcasecmp($this->sortKey($a), $this->sortKey($b)),
        );

        $prevKind = null;

        foreach ($statements as $idx => $statement) {
            if ($idx > 0) {
                $result[] = new TLineBreak(AToken::SYNTHETIC, '');

                if ($statement['kind'] !== $prevKind) {
                    $result[] = new TBlankLine(AToken::SYNTHETIC, '');
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '');
                }
            }

            foreach ($statement['tokens'] as $token) {
                $result[] = $token;
            }

            $prevKind = $statement['kind'];
        }

        // blank line after last import
        $result[] = new TLineBreak(AToken::SYNTHETIC, '');
        $result[] = new TBlankLine(AToken::SYNTHETIC, '');
        $result[] = new TLineBreak(AToken::SYNTHETIC, '');
    }

    /**
     * Filter out unused imports and classify each by kind.
     *
     * @param AToken[][] $block
     * @param array<string, true> $usedNames
     * @return list<array{kind: int, tokens: AToken[]}>
     */
    private function filterAndClassify(array $block, array $usedNames) : array
    {
        $statements = [];

        foreach ($block as $statementTokens) {
            $effectiveName = $this->effectiveName($statementTokens);

            if ($effectiveName === null || ! isset($usedNames[$effectiveName])) {
                continue;
            }

            $statements[] = [
                'kind' => $this->statementKind($statementTokens),
                'tokens' => $statementTokens,
            ];
        }

        return $statements;
    }

    /**
     * Get the name that should be checked against usedNames for an import.
     *
     * @param AToken[] $statementTokens
     */
    private function effectiveName(array $statementTokens) : ?string
    {
        $localName = null;
        $aliasName = null;

        foreach ($statementTokens as $token) {
            if ($token instanceof TUseAlias) {
                $aliasName = $token->text;
            }

            if (
                $token instanceof TQualifiedName
                || $token instanceof TUnqualifiedName
                || $token instanceof TFunctionName
                || $token instanceof TConstName
            ) {
                $parts = explode('\\', $token->text);
                $localName = end($parts);
            }
        }

        return $aliasName ?? $localName;
    }

    private function isUseKeyword(AToken $token) : bool
    {
        return $token instanceof TUse
            || $token instanceof TUseConst
            || $token instanceof TUseFunction;
    }

    private function isSeparator(AToken $token) : bool
    {
        return $token instanceof TWhitespaceEol
            || $token instanceof TLineBreak
            || $token instanceof TBlankLine;
    }

    /**
     * @param AToken[] $statement
     */
    private function statementKind(array $statement) : int
    {
        foreach ($statement as $token) {
            if ($token instanceof TUseConst) {
                return 1;
            }

            if ($token instanceof TUseFunction) {
                return 2;
            }
        }

        return 0; // classlike
    }

    /**
     * @param array{kind: int, tokens: AToken[]} $statement
     */
    private function sortKey(array $statement) : string
    {
        foreach ($statement['tokens'] as $token) {
            if (
                $token instanceof TQualifiedName
                || $token instanceof TUnqualifiedName
            ) {
                return strtolower($token->text);
            }
        }

        return '';
    }

    /**
     * @param array<string, true> $usedNames
     */
    private function extractDocblockNames(
        \PhpStyler\Docblock $docblock,
        array &$usedNames,
    ) : void
    {
        foreach ($docblock->tags as $tag) {
            $segments = preg_split('/[\s|&<>(),{}\[\]]/', $tag->body);

            if ($segments === false) {
                continue;
            }

            foreach ($segments as $segment) {
                $segment = ltrim($segment, '?');
                $segment = rtrim($segment, '.');

                if ($segment !== '' && $segment[0] !== '$') {
                    $usedNames[$segment] = true;
                }
            }
        }
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TConstName;
use PhpStyler\Token\TDocblock;
use PhpStyler\Token\TFunctionCallName;
use PhpStyler\Token\TFunctionCallQualified;
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

class NormalizeImports implements TokenRule
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

            // start collecting a contiguous block of use statements
            $block = [];
            $currentStatement = [$token];
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

                    // check if next token starts another use statement
                    if ($i < $count && $this->isUseKeyword($tokens[$i])) {
                        $currentStatement = [$tokens[$i]];
                        $i ++;
                        continue;
                    }

                    // block ended — collect remaining tokens for usage scan
                    $remainingTokens = array_slice($tokens, $i);
                    $usedNames = $this->collectUsedNames($remainingTokens);
                    $this->emitBlock($block, $usedNames, $result);

                    break;
                }

                $currentStatement[] = $token;
                $i ++;
            }

            // handle edge case: unclosed statement at end of tokens
            if ($currentStatement !== []) {
                foreach ($currentStatement as $t) {
                    $result[] = $t;
                }
            }
        }

        return $result;
    }

    /**
     * @param AToken[] $remainingTokens
     * @return array<string, true>
     */
    private function collectUsedNames(array $remainingTokens) : array
    {
        $usedNames = [];

        foreach ($remainingTokens as $token) {
            // code name references
            if (
                $token instanceof TUnqualifiedName
                || $token instanceof TFunctionCallName
                || $token instanceof TConstName
                || $token instanceof TUnknownString
            ) {
                $usedNames[$token->text] = true;
            }

            // qualified names: first segment may match an import
            if (
                $token instanceof TQualifiedName
                || $token instanceof TFunctionCallQualified
            ) {
                $parts = explode('\\', $token->text);
                $usedNames[$parts[0]] = true;
            }

            // docblock references
            if ($token instanceof TDocblock) {
                $docblock = $token->getDocblock();

                foreach ($docblock->tags as $tag) {
                    $this->extractDocblockNames($tag->body, $usedNames);
                }
            }
        }

        return $usedNames;
    }

    /**
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
        // extract metadata and filter unused
        $statements = [];

        foreach ($block as $statementTokens) {
            $localName = null;
            $aliasName = null;

            foreach ($statementTokens as $token) {
                if ($token instanceof TUseAlias) {
                    $aliasName = $token->text;
                }

                if (
                    $token instanceof TQualifiedName
                    || $token instanceof TUnqualifiedName
                ) {
                    $parts = explode('\\', $token->text);
                    $localName = end($parts);
                }
            }

            $effectiveName = $aliasName ?? $localName;

            if ($effectiveName === null || ! isset($usedNames[$effectiveName])) {
                continue;
            }

            $statements[] = [
                'kind' => $this->statementKind($statementTokens),
                'tokens' => $statementTokens,
            ];
        }

        if ($statements === []) {
            return;
        }

        // sort by kind, then alphabetically within kind
        usort(
            $statements,
            function (array $a, array $b) : int {
                $kindCmp = $a['kind'] <=> $b['kind'];

                if ($kindCmp !== 0) {
                    return $kindCmp;
                }

                return strcasecmp($this->sortKey($a), $this->sortKey($b));
            },
        );

        // use the first token for synthetic positioning
        $firstToken = $statements[0]['tokens'][0];
        $line = $firstToken->line;
        $pos = $firstToken->pos;
        $prevKind = null;

        foreach ($statements as $idx => $statement) {
            if ($idx > 0) {
                if ($statement['kind'] !== $prevKind) {
                    // blank line between different kinds
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                    $result[] = new TBlankLine(AToken::SYNTHETIC, '', $line, $pos);
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                } else {
                    $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
                }
            }

            foreach ($statement['tokens'] as $token) {
                $result[] = $token;
            }

            $prevKind = $statement['kind'];
        }

        // blank line after last import
        $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
        $result[] = new TBlankLine(AToken::SYNTHETIC, '', $line, $pos);
        $result[] = new TLineBreak(AToken::SYNTHETIC, '', $line, $pos);
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
    private function extractDocblockNames(string $body, array &$usedNames) : void
    {
        // split on type-expression delimiters
        $segments = preg_split('/[\s|&<>(),{}\[\]]/', $body);

        if ($segments === false) {
            return;
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

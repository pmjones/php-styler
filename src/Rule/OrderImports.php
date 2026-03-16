<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseConst;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TUseFunction;
use PhpStyler\Token\TUseTrait;
use PhpStyler\Token\TWhitespaceEol;

class OrderImports implements TokenRule
{
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isUseKeyword($token)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // start collecting a contiguous block of use statements
            $block = []; // array of statements, each is [kind, tokens[]]
            $currentStatement = [$token];

            $i ++;

            while ($i < $count) {
                $token = $tokens[$i];

                if ($token instanceof TUseEndSemicolon) {
                    $currentStatement[] = $token;
                    $block[] = [
                        'kind' => $this->statementKind($currentStatement),
                        'tokens' => $currentStatement,
                    ];
                    $currentStatement = [];
                    $i ++;

                    // now consume separator tokens between statements
                    $separators = [];

                    while ($i < $count && $this->isSeparator($tokens[$i])) {
                        $separators[] = $tokens[$i];
                        $i ++;
                    }

                    // check if next token starts another use statement
                    if ($i < $count && $this->isUseKeyword($tokens[$i])) {
                        $currentStatement = [$tokens[$i]];
                        $i ++;
                        continue;
                    }

                    // block ended — rebuild and emit
                    $this->emitBlock($block, $result);

                    // emit any trailing separators that came after the last statement
                    foreach ($separators as $sep) {
                        $result[] = $sep;
                    }

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

    private function isUseKeyword(T $token) : bool
    {
        return (
            $token instanceof TUse
            || $token instanceof TUseConst
            || $token instanceof TUseFunction
        )
            && ! $token instanceof TUseTrait;
    }

    private function isSeparator(T $token) : bool
    {
        return $token instanceof TWhitespaceEol
            || $token instanceof TLineBreak
            || $token instanceof TBlankLine;
    }

    /** @param T[] $statement */
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

    private function sortKey(array $statement) : string
    {
        foreach ($statement['tokens'] as $token) {
            if (
                $token instanceof TQualifiedName || $token instanceof TUnqualifiedName
            ) {
                return strtolower($token->text);
            }
        }

        return '';
    }

    /** @param array[] $block @param T[] &$result */
    private function emitBlock(array $block, array &$result) : void
    {
        if (count($block) === 0) {
            return;
        }

        // sort by kind, then alphabetically within kind
        usort(
            $block,
            function (array $a, array $b) : int {
                $kindCmp = $a['kind'] <=> $b['kind'];

                if ($kindCmp !== 0) {
                    return $kindCmp;
                }

                return strcasecmp($this->sortKey($a), $this->sortKey($b));
            },
        );

        // use the first token for synthetic positioning
        $firstToken = $block[0]['tokens'][0];
        $line = $firstToken->line;
        $pos = $firstToken->pos;
        $prevKind = null;

        foreach ($block as $idx => $statement) {
            if ($idx > 0) {
                if ($statement['kind'] !== $prevKind) {
                    // blank line between different kinds
                    $result[] = new TLineBreak(T::SYNTHETIC, '', $line, $pos);
                    $result[] = new TBlankLine(T::SYNTHETIC, '', $line, $pos);
                    $result[] = new TLineBreak(T::SYNTHETIC, '', $line, $pos);
                } else {
                    $result[] = new TLineBreak(T::SYNTHETIC, '', $line, $pos);
                }
            }

            foreach ($statement['tokens'] as $token) {
                $result[] = $token;
            }

            $prevKind = $statement['kind'];
        }
    }
}

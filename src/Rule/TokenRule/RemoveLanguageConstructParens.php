<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\ALanguageConstruct;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TExpressionClosingParen;
use PhpStyler\Token\TExpressionOpeningParen;
use PhpStyler\Token\TSpace;

class RemoveLanguageConstructParens extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        // map token object IDs to indices for O(1) closer lookup

        /** @var array<int, int> */
        $indexMap = [];

        foreach ($tokens as $idx => $t) {
            $indexMap[spl_object_id($t)] = (int) $idx;
        }

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! ($token instanceof ALanguageConstruct)) {
                $result[] = $token;
                continue;
            }

            // find opening paren
            $openerIdx = $this->findNextContent($tokens, $i + 1, $count);

            if (
                $openerIdx === null
                || ! ($tokens[$openerIdx] instanceof TExpressionOpeningParen)
            ) {
                $result[] = $token;
                continue;
            }

            // find matching closer via link
            $closer = $tokens[$openerIdx]->closingToken;

            // Resilience guards: a correctly-paired TExpressionOpeningParen
            // has a TExpressionClosingParen `closingToken` whose object ID
            // is present in indexMap. If an upstream rule has produced a
            // malformed stream (wrong-typed closer, or closer absent from
            // this tokens array), pass the construct through unchanged.
            // Pinned by testMalformedClosingToken* in this test file.
            if (! ($closer instanceof TExpressionClosingParen)) {
                $result[] = $token;
                continue;
            }

            $closerIdx = $indexMap[spl_object_id($closer)] ?? null;

            if ($closerIdx === null) {
                $result[] = $token;
                continue;
            }

            // only remove parens when they wrap the entire expression before ;
            $afterIdx = $this->findNextContent($tokens, $closerIdx + 1, $count);

            if ($afterIdx === null || $tokens[$afterIdx]->text !== ';') {
                $result[] = $token;
                continue;
            }

            // emit construct + space + contents (without parens)
            $result[] = $token;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');

            for ($j = $openerIdx + 1; $j < $closerIdx; $j ++) {
                $result[] = $tokens[$j];
            }

            $i = $closerIdx;
        }

        return $result;
    }
}

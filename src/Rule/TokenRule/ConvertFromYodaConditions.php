<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AComparisonOperator;
use PhpStyler\Token\ALiteral;
use PhpStyler\Token\AToken;
use PhpStyler\Token\AUnaryPrefixOperator;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TVariable;

class ConvertFromYodaConditions extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        /** @var AToken[] $result */
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! ($token instanceof AComparisonOperator)) {
                $result[] = $token;
                continue;
            }

            $variableIdx = $this->findVariableAfter($tokens, $i + 1, $count);
            $literalInfo = $this->findLiteralBefore($result);

            if ($variableIdx === null || $literalInfo === null) {
                $result[] = $token;
                continue;
            }

            $variable = $tokens[$variableIdx];
            $literal = $result[$literalInfo['literalIdx']];
            $prefix = $literalInfo['prefix'];

            array_splice($result, $literalInfo['spliceFrom']);

            $result[] = $variable;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            $result[] = $token;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');

            if ($prefix !== null) {
                $result[] = $prefix;
            }

            $result[] = $literal;

            $i = $variableIdx;
        }

        return $result;
    }

    /**
     * @param AToken[] $tokens
     */
    private function findVariableAfter(array $tokens, int $from, int $count) : ?int
    {
        $idx = $this->findNextContent($tokens, $from, $count);

        if ($idx !== null && $tokens[$idx] instanceof TVariable) {
            return $idx;
        }

        return null;
    }

    /**
     * @param AToken[] $result
     * @return ?array{literalIdx: int, prefix: ?AToken, spliceFrom: int}
     */
    private function findLiteralBefore(array $result) : ?array
    {
        $literalIdx = $this->findPrevContent($result);

        if ($literalIdx === null || ! ($result[$literalIdx] instanceof ALiteral)) {
            return null;
        }

        $prefixIdx = $this->findPrevContent(array_slice($result, 0, $literalIdx));

        $prefix = null;
        $spliceFrom = $literalIdx;

        if (
            $prefixIdx !== null
            && $result[$prefixIdx] instanceof AUnaryPrefixOperator
        ) {
            $prefix = $result[$prefixIdx];
            $spliceFrom = $prefixIdx;
        }

        return [
            'literalIdx' => $literalIdx,
            'prefix' => $prefix,
            'spliceFrom' => $spliceFrom,
        ];
    }
}

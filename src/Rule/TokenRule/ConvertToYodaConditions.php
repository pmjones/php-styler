<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AComparisonOperator;
use PhpStyler\Token\ALiteral;
use PhpStyler\Token\AToken;
use PhpStyler\Token\AUnaryPrefixOperator;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TVariable;

class ConvertToYodaConditions extends ATokenRule
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

            $literalIdx = $this->findLiteralAfter($tokens, $i + 1, $count);
            $variableIdx = $this->findVariableBefore($result);

            if ($literalIdx === null || $variableIdx === null) {
                $result[] = $token;
                continue;
            }

            $literal = $tokens[$literalIdx];
            $variable = $result[$variableIdx];

            array_splice($result, $variableIdx);

            $result[] = $literal;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            $result[] = $token;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            $result[] = $variable;

            $i = $literalIdx;
        }

        return $result;
    }

    /**
     * @param AToken[] $tokens
     */
    private function findLiteralAfter(array $tokens, int $from, int $count) : ?int
    {
        $idx = $this->findNextContent($tokens, $from, $count);

        if ($idx !== null && $tokens[$idx] instanceof ALiteral) {
            return $idx;
        }

        return null;
    }

    /**
     * @param AToken[] $result
     */
    private function findVariableBefore(array $result) : ?int
    {
        $idx = $this->findPrevContent($result);

        if ($idx === null || ! ($result[$idx] instanceof TVariable)) {
            return null;
        }

        $beforeIdx = $this->findPrevContent(array_slice($result, 0, $idx));

        if (
            $beforeIdx !== null
            && $result[$beforeIdx] instanceof AUnaryPrefixOperator
        ) {
            return null;
        }

        return $idx;
    }
}

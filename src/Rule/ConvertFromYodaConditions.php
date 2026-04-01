<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TFalse;
use PhpStyler\Token\TFloatLiteral;
use PhpStyler\Token\TIntegerLiteral;
use PhpStyler\Token\TIsEqual;
use PhpStyler\Token\TIsIdentical;
use PhpStyler\Token\TIsNotEqual;
use PhpStyler\Token\TIsNotIdentical;
use PhpStyler\Token\TNot;
use PhpStyler\Token\TNull;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TStringLiteral;
use PhpStyler\Token\TTilde;
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnaryMinus;
use PhpStyler\Token\TUnaryPlus;
use PhpStyler\Token\TVariable;

class ConvertFromYodaConditions implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! $this->isComparison($token)) {
                $result[] = $token;
                continue;
            }

            // look forward past optional TSpace for a TVariable
            $variable = null;
            $skip = 0;
            $j = $i + 1;

            if ($j < $count && $tokens[$j] instanceof TSpace) {
                $j ++;
                $skip ++;
            }

            if ($j < $count && $tokens[$j] instanceof TVariable) {
                $variable = $tokens[$j];
                $skip ++;
            } else {
                $result[] = $token;
                continue;
            }

            // look backward in $result for a literal (skipping TSplit and TSpace)
            $resultCount = count($result);

            if ($resultCount < 1) {
                $result[] = $token;
                continue;
            }

            $backIdx = $resultCount - 1;

            while ($backIdx >= 0 && $result[$backIdx] instanceof TSplit) {
                $backIdx --;
            }

            if ($backIdx >= 0 && $result[$backIdx] instanceof TSpace) {
                $backIdx --;
            }

            if ($backIdx < 0 || ! $this->isLiteral($result[$backIdx])) {
                $result[] = $token;
                continue;
            }

            $literal = $result[$backIdx];
            $literalIdx = $backIdx;

            // check for optional unary prefix before the literal
            $prefix = null;
            $prefixIdx = $literalIdx - 1;

            if ($prefixIdx >= 0 && $result[$prefixIdx] instanceof TSpace) {
                $prefixIdx --;
            }

            if ($prefixIdx >= 0 && $this->isUnaryPrefix($result[$prefixIdx])) {
                $prefix = $result[$prefixIdx];
            }

            // pop everything from the earliest position to end of $result
            $popFrom = $prefix !== null ? $prefixIdx : $literalIdx;
            array_splice($result, $popFrom);

            // emit: variable, TSpace, comparison, TSpace, [prefix], literal
            $result[] = $variable;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            $result[] = $token;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');

            if ($prefix !== null) {
                $result[] = $prefix;
            }

            $result[] = $literal;

            // skip consumed forward tokens
            $i += $skip;
        }

        return $result;
    }

    private function isComparison(AToken $token) : bool
    {
        return $token instanceof TIsIdentical
            || $token instanceof TIsNotIdentical
            || $token instanceof TIsEqual
            || $token instanceof TIsNotEqual;
    }

    private function isLiteral(AToken $token) : bool
    {
        return $token instanceof TNull
            || $token instanceof TTrue
            || $token instanceof TFalse
            || $token instanceof TIntegerLiteral
            || $token instanceof TFloatLiteral
            || $token instanceof TStringLiteral;
    }

    private function isUnaryPrefix(AToken $token) : bool
    {
        return $token instanceof TNot
            || $token instanceof TUnaryMinus
            || $token instanceof TUnaryPlus
            || $token instanceof TTilde;
    }
}

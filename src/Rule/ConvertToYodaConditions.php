<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
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
use PhpStyler\Token\TStringLiteral;
use PhpStyler\Token\TTilde;
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnaryMinus;
use PhpStyler\Token\TUnaryPlus;
use PhpStyler\Token\TVariable;

class ConvertToYodaConditions implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
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

            // look forward past optional TSpace for a literal
            $forwardSpace = null;
            $literal = null;
            $skip = 0;
            $j = $i + 1;

            if ($j < $count && $tokens[$j] instanceof TSpace) {
                $forwardSpace = $tokens[$j];
                $j ++;
                $skip ++;
            }

            if ($j < $count && $this->isLiteral($tokens[$j])) {
                $literal = $tokens[$j];
                $skip ++;
            } else {
                $result[] = $token;
                continue;
            }

            // look backward in $result for TVariable (with optional trailing TSpace)
            $resultCount = count($result);

            if ($resultCount < 1) {
                $result[] = $token;
                continue;
            }

            $backIdx = $resultCount - 1;
            $trailingSpace = null;

            if ($result[$backIdx] instanceof TSpace) {
                $trailingSpace = $result[$backIdx];
                $backIdx --;
            }

            if ($backIdx < 0 || ! ($result[$backIdx] instanceof TVariable)) {
                $result[] = $token;
                continue;
            }

            // safety check: token before TVariable must not be a unary prefix operator
            $checkIdx = $backIdx - 1;

            if ($checkIdx >= 0 && $result[$checkIdx] instanceof TSpace) {
                $checkIdx --;
            }

            if ($checkIdx >= 0 && $this->isUnaryPrefix($result[$checkIdx])) {
                $result[] = $token;
                continue;
            }

            $variable = $result[$backIdx];

            // pop variable (and trailing space) from $result
            if ($trailingSpace !== null) {
                array_pop($result);
            }

            array_pop($result);

            // emit: literal, TSpace, comparison, TSpace, variable
            $result[] = $literal;
            $result[] = new TSpace(T::SYNTHETIC, ' ');
            $result[] = $token;
            $result[] = new TSpace(T::SYNTHETIC, ' ');
            $result[] = $variable;

            // skip consumed forward tokens
            $i += $skip;
        }

        return $result;
    }

    private function isComparison(T $token) : bool
    {
        return $token instanceof TIsIdentical
            || $token instanceof TIsNotIdentical
            || $token instanceof TIsEqual
            || $token instanceof TIsNotEqual;
    }

    private function isLiteral(T $token) : bool
    {
        return $token instanceof TNull
            || $token instanceof TTrue
            || $token instanceof TFalse
            || $token instanceof TIntegerLiteral
            || $token instanceof TFloatLiteral
            || $token instanceof TStringLiteral;
    }

    private function isUnaryPrefix(T $token) : bool
    {
        return $token instanceof TNot
            || $token instanceof TUnaryMinus
            || $token instanceof TUnaryPlus
            || $token instanceof TTilde;
    }
}

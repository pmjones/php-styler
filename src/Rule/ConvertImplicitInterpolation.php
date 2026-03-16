<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TCurlyClose;
use PhpStyler\Token\TCurlyOpen;
use PhpStyler\Token\TEncapsedArrayElementClosingBracket;
use PhpStyler\Token\TEncapsedArrayElementOpeningBracket;
use PhpStyler\Token\TEncapsedObjectOperator;
use PhpStyler\Token\TEncapsedPropertyAccessName;
use PhpStyler\Token\TEncapsedVariable;

class ConvertImplicitInterpolation implements TokenRule
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

            if (! ($token instanceof TEncapsedVariable)) {
                $result[] = $token;
                continue;
            }

            // Already wrapped in curlies
            $last = end($result);

            if ($last instanceof TCurlyOpen) {
                $result[] = $token;
                continue;
            }

            // Insert TCurlyOpen before variable
            $result[] = new TCurlyOpen(T_CURLY_OPEN, '{', $token->line, $token->pos);
            $result[] = $token;

            // Consume trailing access tokens
            if (
                isset($tokens[$i + 1])
                && $tokens[$i + 1] instanceof TEncapsedArrayElementOpeningBracket
            ) {
                $i ++;
                $result[] = $tokens[$i];

                while (
                    isset($tokens[$i + 1])
                    && ! ($tokens[$i] instanceof TEncapsedArrayElementClosingBracket)
                ) {
                    $i ++;
                    $result[] = $tokens[$i];
                }
            } elseif (
                isset($tokens[$i + 1])
                && $tokens[$i + 1] instanceof TEncapsedObjectOperator
            ) {
                $i ++;
                $result[] = $tokens[$i];

                if (
                    isset($tokens[$i + 1])
                    && $tokens[$i + 1] instanceof TEncapsedPropertyAccessName
                ) {
                    $i ++;
                    $result[] = $tokens[$i];
                }
            }

            // Insert TCurlyClose
            $closePos = $tokens[$i];
            $result[] = new TCurlyClose(ord('}'), '}', $closePos->line, $closePos->pos);
        }

        return $result;
    }
}

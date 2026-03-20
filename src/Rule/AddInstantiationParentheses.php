<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAnonymousClass;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TNew;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TVariable;

class AddInstantiationParentheses implements TokenRule
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

            if (! ($token instanceof TNew)) {
                $result[] = $token;
                continue;
            }

            $result[] = $token;
            $j = $i + 1;

            // skip past TSpace
            while ($j < $count && $tokens[$j] instanceof TSpace) {
                $result[] = $tokens[$j];
                $j ++;
            }

            if ($j >= $count) {
                $i = $j - 1;
                continue;
            }

            $nameToken = $tokens[$j];

            if ($nameToken instanceof TAnonymousClass) {
                $i = $j - 1;
                continue;
            }

            if (
                ! ($nameToken instanceof TUnqualifiedName)
                && ! ($nameToken instanceof TFullyQualifiedName)
                && ! ($nameToken instanceof TQualifiedName)
                && ! ($nameToken instanceof TVariable)
            ) {
                $i = $j - 1;
                continue;
            }

            $result[] = $nameToken;
            $j ++;

            // check if next token is TArgsOpeningParen (possibly after TSpace)
            $k = $j;

            if ($k < $count && $tokens[$k] instanceof TSpace) {
                $k ++;
            }

            if ($k < $count && $tokens[$k] instanceof TArgsOpeningParen) {
                $i = $j - 1;
                continue;
            }

            $result[] = new TArgsOpeningParen(AToken::SYNTHETIC, '(');
            $result[] = new TArgsClosingParen(AToken::SYNTHETIC, ')');
            $i = $j - 1;
        }

        return $result;
    }
}

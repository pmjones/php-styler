<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TArrayElementOpeningBracket;
use PhpStyler\Token\TDoubleColon;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TNew;
use PhpStyler\Token\TNullsafeObjectOperator;
use PhpStyler\Token\TObjectOperator;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TVariable;

class InsertNewParens extends ATokenRule
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

            // find name token
            $nameIndex = $this->findNextContent($tokens, $i + 1, $count);

            if ($nameIndex === null || ! $this->isNameToken($tokens[$nameIndex])) {
                continue;
            }

            // find what follows the name
            $afterIndex = $this->findNextContent($tokens, $nameIndex + 1, $count);

            // already has parens
            if (
                $afterIndex !== null
                && $tokens[$afterIndex] instanceof TArgsOpeningParen
            ) {
                continue;
            }

            // variable followed by property/method/element access: skip
            if (
                $tokens[$nameIndex] instanceof TVariable
                && $afterIndex !== null
                && $this->isContinuationToken($tokens[$afterIndex])
            ) {
                continue;
            }

            // emit tokens up to and including name, then inject parens
            for ($j = $i + 1; $j <= $nameIndex; $j ++) {
                $result[] = $tokens[$j];
            }

            $opener = new TArgsOpeningParen(AToken::SYNTHETIC, '(');
            $closer = new TArgsClosingParen(AToken::SYNTHETIC, ')');
            AToken::pair($opener, $closer);
            $result[] = $opener;
            $result[] = $closer;

            $i = $nameIndex;
        }

        return $result;
    }

    private function isNameToken(AToken $token) : bool
    {
        return $token instanceof TUnqualifiedName
            || $token instanceof TQualifiedName
            || $token instanceof TFullyQualifiedName
            || $token instanceof TVariable;
    }

    private function isContinuationToken(AToken $token) : bool
    {
        return $token instanceof TObjectOperator
            || $token instanceof TNullsafeObjectOperator
            || $token instanceof TDoubleColon
            || $token instanceof TArrayElementOpeningBracket;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TArrayClosingBracket;
use PhpStyler\Token\TArrayConstruct;
use PhpStyler\Token\TArrayConstructClosingParen;
use PhpStyler\Token\TArrayConstructOpeningParen;
use PhpStyler\Token\TArrayOpeningBracket;

class ConvertToShortArraySyntax extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];

        foreach ($tokens as $token) {
            if ($token instanceof TArrayConstruct) {
                continue;
            }

            if ($token instanceof TArrayConstructOpeningParen) {
                $new = new TArrayOpeningBracket(AToken::SYNTHETIC, '[');
                $new->closingToken = $token->closingToken;
                $result[] = $new;
                continue;
            }

            if ($token instanceof TArrayConstructClosingParen) {
                $new = new TArrayClosingBracket(AToken::SYNTHETIC, ']');
                $new->openingToken = $token->openingToken;
                $result[] = $new;
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }
}

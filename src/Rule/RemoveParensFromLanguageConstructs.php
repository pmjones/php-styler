<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TEcho;
use PhpStyler\Token\TExpressionClosingParen;
use PhpStyler\Token\TExpressionOpeningParen;
use PhpStyler\Token\TInclude;
use PhpStyler\Token\TIncludeOnce;
use PhpStyler\Token\TPrint;
use PhpStyler\Token\TRequire;
use PhpStyler\Token\TRequireOnce;
use PhpStyler\Token\TReturn;
use PhpStyler\Token\TSemicolon;
use PhpStyler\Token\TSpace;

class RemoveParensFromLanguageConstructs implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $skipClosingIds = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            // skip closing parens that we've marked for removal
            if (
                $token instanceof TExpressionClosingParen
                && isset($skipClosingIds[spl_object_id($token)])
            ) {
                continue;
            }

            if (! $this->isLanguageConstruct($token)) {
                $result[] = $token;
                continue;
            }

            // look forward past optional TSpace for TExpressionOpeningParen
            $j = $i + 1;

            if ($j < $count && $tokens[$j] instanceof TSpace) {
                $j ++;
            }

            if ($j >= $count || ! $tokens[$j] instanceof TExpressionOpeningParen) {
                $result[] = $token;
                continue;
            }

            $openParen = $tokens[$j];

            if ($openParen->closingToken === null) {
                $result[] = $token;
                continue;
            }

            $closeParen = $openParen->closingToken;

            // find the closing paren's index in the token array
            $closeIdx = null;

            for ($k = $j + 1; $k < $count; $k ++) {
                if ($tokens[$k] === $closeParen) {
                    $closeIdx = $k;
                    break;
                }
            }

            if ($closeIdx === null) {
                $result[] = $token;
                continue;
            }

            // check that token after closing paren is a semicolon (or TSpace then semicolon)
            $afterIdx = $closeIdx + 1;

            if ($afterIdx < $count && $tokens[$afterIdx] instanceof TSpace) {
                $afterIdx ++;
            }

            if ($afterIdx >= $count || $tokens[$afterIdx]->text !== ';') {
                $result[] = $token;
                continue;
            }

            // remove parens: emit construct keyword, ensure space, skip opening paren
            $result[] = $token;
            $result[] = new TSpace(T::SYNTHETIC, ' ');

            // skip the opening paren (and any space before it)
            $i = $j; // skip to after opening paren

            // mark closing paren for removal
            $skipClosingIds[spl_object_id($closeParen)] = true;
        }

        return $result;
    }

    private function isLanguageConstruct(T $token) : bool
    {
        return $token instanceof TEcho
            || $token instanceof TPrint
            || $token instanceof TReturn
            || $token instanceof TInclude
            || $token instanceof TIncludeOnce
            || $token instanceof TRequire
            || $token instanceof TRequireOnce;
    }
}

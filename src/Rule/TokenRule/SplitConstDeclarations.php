<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TConst;
use PhpStyler\Token\TConstComma;
use PhpStyler\Token\TConstEndSemicolon;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TNamespaceConstEndSemicolon;
use PhpStyler\Token\TSpace;

class SplitConstDeclarations extends SplitDeclarations
{
    protected function isComma(AToken $token) : bool
    {
        return $token instanceof TConstComma;
    }

    protected function isMarker(AToken $token) : bool
    {
        return $token instanceof TConst;
    }

    protected function isPrefixToken(AToken $token) : bool
    {
        return $token->is([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL]);
    }

    /**
     * @param AToken[] $result
     * @param AToken[] $tokens
     * @param AToken[] $prefix
     */
    protected function emitSplit(
        array &$result,
        array $tokens,
        int $i,
        int $count,
        array $prefix,
    ) : void
    {
        // determine semicolon class by scanning forward
        $semicolonClass = TConstEndSemicolon::class;

        for ($j = $i + 1; $j < $count; $j ++) {
            if ($tokens[$j] instanceof TConstEndSemicolon) {
                break;
            }

            if ($tokens[$j] instanceof TNamespaceConstEndSemicolon) {
                $semicolonClass = TNamespaceConstEndSemicolon::class;
                break;
            }
        }

        $result[] = new $semicolonClass(AToken::SYNTHETIC, ';');
        $result[] = new TLineBreak(AToken::SYNTHETIC, '');

        foreach ($prefix as $t) {
            $result[] = clone $t;
            $result[] = new TSpace(AToken::SYNTHETIC, ' ');
        }

        $result[] = new TConst(AToken::SYNTHETIC, 'const');
        $result[] = new TSpace(AToken::SYNTHETIC, ' ');
    }
}

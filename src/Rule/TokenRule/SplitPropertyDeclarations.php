<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TPropertyComma;
use PhpStyler\Token\TPropertyEndSemicolon;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TVariable;

class SplitPropertyDeclarations extends SplitDeclarations
{
    protected function isComma(AToken $token) : bool
    {
        return $token instanceof TPropertyComma;
    }

    protected function isMarker(AToken $token) : bool
    {
        return $token instanceof TVariable;
    }

    protected function isPrefixToken(AToken $token) : bool
    {
        return $token->is([
                T_PUBLIC,
                T_PROTECTED,
                T_PRIVATE,
                T_STATIC,
                T_READONLY,
                T_VAR,
                T_STRING,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
                T_NAME_RELATIVE,
                T_ARRAY,
                T_CALLABLE,
            ])
            || $token->text === '?'
            || $token->text === '|'
            || $token->text === '&';
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
        $result[] = new TPropertyEndSemicolon(AToken::SYNTHETIC, ';');
        $result[] = new TLineBreak(AToken::SYNTHETIC, '');

        foreach ($prefix as $idx => $t) {
            $result[] = clone $t;

            // add space after modifiers and type names, but not after
            // type operators (|, &, ?) or before them
            $nextPrefix = $prefix[$idx + 1] ?? null;
            $isOperator = $t->text === '|' || $t->text === '&' || $t->text === '?';

            $nextIsOperator = $nextPrefix !== null
                && ($nextPrefix->text === '|' || $nextPrefix->text === '&');

            if (! $isOperator && ! $nextIsOperator) {
                $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            }
        }
    }
}

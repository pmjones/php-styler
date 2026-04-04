<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AClosingStructure;
use PhpStyler\Token\AModifier;
use PhpStyler\Token\AnOpeningStructure;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TAnonymousClass;
use PhpStyler\Token\TAnonymousFunction;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TClasslikeOpeningBrace;
use PhpStyler\Token\TConst;
use PhpStyler\Token\TFunction;
use PhpStyler\Token\TPrivate;
use PhpStyler\Token\TProtected;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TSpace;

class InsertPublicVisibility extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];

        /** @var list<'class'|'other'> */
        $scopeStack = [];
        $hasVisibility = false;
        $isAnonymousClass = false;

        foreach ($tokens as $token) {
            // track whether the last anonymous construct was a class or function
            if ($token instanceof TAnonymousClass) {
                $isAnonymousClass = true;
            } elseif ($token instanceof TAnonymousFunction) {
                $isAnonymousClass = false;
            }

            // manage scope stack
            if ($token instanceof AnOpeningStructure) {
                if (
                    $token instanceof TClasslikeOpeningBrace
                    || (
                        $token instanceof TAnonymousOpeningBrace
                        && $isAnonymousClass
                    )
                ) {
                    $scopeStack[] = 'class';
                } else {
                    $scopeStack[] = 'other';
                }

                $result[] = $token;
                continue;
            }

            if (
                $token instanceof AClosingStructure
                && $token->openingToken instanceof AnOpeningStructure
            ) {
                array_pop($scopeStack);
                $result[] = $token;
                continue;
            }

            // insert public if needed (check BEFORE updating visibility state)
            if (
                $scopeStack !== []
                && end($scopeStack) === 'class'
                && ($token instanceof TFunction || $token instanceof TConst)
                && ! $hasVisibility
            ) {
                $result[] = new TPublic(AToken::SYNTHETIC, 'public');
                $result[] = new TSpace(AToken::SYNTHETIC, ' ');
            }

            $result[] = $token;

            // update visibility tracking AFTER the check
            if (
                $token instanceof TPublic
                || $token instanceof TProtected
                || $token instanceof TPrivate
            ) {
                $hasVisibility = true;
            } elseif (! ($token instanceof AModifier) && ! $token->isIgnorable()) {
                $hasVisibility = false;
            }
        }

        return $result;
    }
}

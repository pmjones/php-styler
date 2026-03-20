<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAbstract;
use PhpStyler\Token\TAnonymousClass;
use PhpStyler\Token\TAnonymousFunction;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TClasslikeOpeningBrace;
use PhpStyler\Token\TClosingStructure;
use PhpStyler\Token\TConst;
use PhpStyler\Token\TFinal;
use PhpStyler\Token\TFunction;
use PhpStyler\Token\TOpeningStructure;
use PhpStyler\Token\TPrivate;
use PhpStyler\Token\TProtected;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TReadonly;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TStatic;
use PhpStyler\Token\TVar;

class AddMissingVisibility implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $structureStack = [];
        $anonStack = [];

        foreach ($tokens as $token) {
            // track anonymous class vs anonymous function
            if ($token instanceof TAnonymousClass) {
                $anonStack[] = 'class';
            } elseif ($token instanceof TAnonymousFunction) {
                $anonStack[] = 'function';
            }

            // manage structure stack
            if ($token instanceof TClasslikeOpeningBrace) {
                $structureStack[] = true;
            } elseif ($token instanceof TAnonymousOpeningBrace) {
                $isClass = array_pop($anonStack) === 'class';
                $structureStack[] = $isClass;
            } elseif ($token instanceof TOpeningStructure) {
                $structureStack[] = false;
            } elseif ($token instanceof TClosingStructure) {
                array_pop($structureStack);
            }

            // insert public if needed
            $atClassBody = $structureStack !== [] && end($structureStack) === true;

            if (
                $atClassBody
                && ($token instanceof TFunction || $token instanceof TConst)
            ) {
                if (! $this->hasVisibilityBefore($result)) {
                    $result[] = new TPublic(
                        T_PUBLIC,
                        'public',
                        $token->line,
                        $token->pos,
                    );
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                }
            }

            $result[] = $token;
        }

        return $result;
    }

    /**
     * @param AToken[] $result
     */
    private function hasVisibilityBefore(array $result) : bool
    {
        for ($i = count($result) - 1; $i >= 0; $i --) {
            $prev = $result[$i];

            if (
                $prev instanceof TSpace
                || $prev instanceof TStatic
                || $prev instanceof TReadonly
                || $prev instanceof TAbstract
                || $prev instanceof TFinal
            ) {
                continue;
            }

            return $prev instanceof TPublic
                || $prev instanceof TProtected
                || $prev instanceof TPrivate
                || $prev instanceof TVar;
        }

        return false;
    }
}

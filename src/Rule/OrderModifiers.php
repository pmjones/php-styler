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
use PhpStyler\Token\TPrivateSet;
use PhpStyler\Token\TProtected;
use PhpStyler\Token\TProtectedSet;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TPublicSet;
use PhpStyler\Token\TReadonly;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TStatic;
use PhpStyler\Token\TVar;

class OrderModifiers implements TokenRule
{
    /**
     * @var array<class-string<AToken>, int>
     */
    private const PRIORITY = [
        TAbstract::class => 1,
        TFinal::class => 1,
        TPublic::class => 2,
        TProtected::class => 2,
        TPrivate::class => 2,
        TVar::class => 2,
        TPublicSet::class => 3,
        TProtectedSet::class => 3,
        TPrivateSet::class => 3,
        TStatic::class => 4,
        TReadonly::class => 5,
    ];

    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $structureStack = [];
        $anonStack = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isModifier($token)) {
                $this->updateStructureStack($token, $structureStack, $anonStack);

                if (
                    $this->atClassBody($structureStack)
                    && ($token instanceof TFunction || $token instanceof TConst)
                    && ! $this->hasVisibilityBefore($result)
                ) {
                    $result[] = new TPublic(
                        T_PUBLIC,
                        'public',
                        $token->line,
                        $token->pos,
                    );
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                }

                $result[] = $token;
                $i ++;
                continue;
            }

            // collect contiguous modifier group (modifiers + interleaving TSpace)
            $groupStart = $i;
            $modifiers = [];
            $modifiers[] = $token;
            $i ++;

            while ($i < $count) {
                if (
                    $tokens[$i] instanceof TSpace
                    && isset($tokens[$i + 1])
                    && $this->isModifier($tokens[$i + 1])
                ) {
                    $i ++; // skip TSpace
                    $modifiers[] = $tokens[$i];
                    $i ++;
                } else {
                    break;
                }
            }

            // convert var to public
            foreach ($modifiers as $idx => $mod) {
                if ($mod instanceof TVar) {
                    $modifiers[$idx] = new TPublic(
                        T_PUBLIC,
                        'public',
                        $mod->line,
                        $mod->pos,
                    );
                }
            }

            // add missing visibility for class-body function/const
            $nextIdx = $i;

            if (isset($tokens[$nextIdx]) && $tokens[$nextIdx] instanceof TSpace) {
                $nextIdx ++;
            }

            $nextToken = $tokens[$nextIdx] ?? null;

            if (
                $this->atClassBody($structureStack)
                && ($nextToken instanceof TFunction || $nextToken instanceof TConst)
                && ! $this->hasVisibilityIn($modifiers)
            ) {
                $modifiers[] = new TPublic(
                    T_PUBLIC,
                    'public',
                    $nextToken->line,
                    $nextToken->pos,
                );
            }

            if (count($modifiers) <= 1) {
                // single modifier, no reordering needed
                foreach ($modifiers as $idx => $mod) {
                    if ($idx > 0) {
                        $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                    }

                    $result[] = $mod;
                }

                continue;
            }

            // check if already in correct order
            $alreadySorted = true;

            for ($k = 1; $k < count($modifiers); $k ++) {
                if (
                    $this
                    ->priority($modifiers[$k]) < $this
                    ->priority($modifiers[$k - 1])
                ) {
                    $alreadySorted = false;
                    break;
                }
            }

            if ($alreadySorted) {
                // rebuild with modifiers and spaces in original order
                foreach ($modifiers as $idx => $mod) {
                    if ($idx > 0) {
                        $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                    }

                    $result[] = $mod;
                }

                continue;
            }

            // sort by canonical priority (stable sort)
            usort(
                $modifiers,
                fn (AToken $a, AToken $b)
                    => $this->priority($a) <=> $this->priority($b),
            );

            // rebuild: modifier, space, modifier, space, ...
            foreach ($modifiers as $idx => $mod) {
                if ($idx > 0) {
                    $result[] = new TSpace(AToken::SYNTHETIC, ' ');
                }

                $result[] = $mod;
            }
        }

        return $result;
    }

    private function isModifier(AToken $token) : bool
    {
        return isset(self::PRIORITY[get_class($token)]);
    }

    private function priority(AToken $token) : int
    {
        return self::PRIORITY[get_class($token)];
    }

    /**
     * @param array<int, bool> $structureStack
     * @param array<int, string> $anonStack
     */
    private function updateStructureStack(
        AToken $token,
        array &$structureStack,
        array &$anonStack,
    ) : void
    {
        if ($token instanceof TAnonymousClass) {
            $anonStack[] = 'class';
        } elseif ($token instanceof TAnonymousFunction) {
            $anonStack[] = 'function';
        }

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
    }

    /**
     * @param array<int, bool> $structureStack
     */
    private function atClassBody(array $structureStack) : bool
    {
        return $structureStack !== [] && end($structureStack) === true;
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

    /**
     * @param AToken[] $modifiers
     */
    private function hasVisibilityIn(array $modifiers) : bool
    {
        foreach ($modifiers as $mod) {
            if (
                $mod instanceof TPublic
                || $mod instanceof TProtected
                || $mod instanceof TPrivate
                || $mod instanceof TVar
            ) {
                return true;
            }
        }

        return false;
    }
}

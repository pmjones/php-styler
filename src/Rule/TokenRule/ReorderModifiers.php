<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AModifier;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TAbstract;
use PhpStyler\Token\TFinal;
use PhpStyler\Token\TPrivate;
use PhpStyler\Token\TPrivateSet;
use PhpStyler\Token\TProtected;
use PhpStyler\Token\TProtectedSet;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TPublicSet;
use PhpStyler\Token\TReadonly;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TStatic;

class ReorderModifiers extends ATokenRule
{
    private const PRIORITY = [
        TAbstract::class => 1,
        TFinal::class => 1,
        TPublic::class => 2,
        TProtected::class => 2,
        TPrivate::class => 2,
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
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            if (! $this->isModifier($tokens[$i])) {
                $result[] = $tokens[$i];
                continue;
            }

            // collect the full range of this modifier group
            $group = [];
            $modifiers = [];
            $modifierPositions = [];
            $j = $i;

            while ($j < $count) {
                $t = $tokens[$j];

                if ($this->isModifier($t)) {
                    $modifierPositions[] = count($group);
                    $modifiers[] = $t;
                    $group[] = $t;
                    $j ++;
                } elseif ($t instanceof TSpace || $t instanceof TSplit) {
                    $group[] = $t;
                    $j ++;
                } else {
                    break;
                }
            }

            // sort modifiers by priority, place back at original positions
            usort(
                $modifiers,
                fn (AToken $a, AToken $b)
                    => (self::PRIORITY[get_class($a)] ?? 99)
                        <=> (self::PRIORITY[get_class($b)] ?? 99),
            );

            foreach ($modifierPositions as $k => $pos) {
                $group[$pos] = $modifiers[$k];
            }

            // emit the reordered group
            foreach ($group as $t) {
                $result[] = $t;
            }

            $i = $j - 1;
        }

        return $result;
    }

    private function isModifier(AToken $token) : bool
    {
        return $token instanceof AModifier;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
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
use PhpStyler\Token\TStatic;
use PhpStyler\Token\TVar;

class OrderModifiers implements TokenRule
{
    /**
     * @var array<class-string<T>, int>
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
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isModifier($token)) {
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

            if (count($modifiers) <= 1) {
                // single modifier, no reordering needed
                foreach ($modifiers as $idx => $mod) {
                    if ($idx > 0) {
                        $result[] = new TSpace(T::SYNTHETIC, ' ');
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
                        $result[] = new TSpace(T::SYNTHETIC, ' ');
                    }

                    $result[] = $mod;
                }

                continue;
            }

            // sort by canonical priority (stable sort)
            usort(
                $modifiers,
                fn (T $a, T $b)
                    => $this->priority($a) <=> $this->priority($b),
            );

            // rebuild: modifier, space, modifier, space, ...
            foreach ($modifiers as $idx => $mod) {
                if ($idx > 0) {
                    $result[] = new TSpace(T::SYNTHETIC, ' ');
                }

                $result[] = $mod;
            }
        }

        return $result;
    }

    private function isModifier(T $token) : bool
    {
        return isset(self::PRIORITY[get_class($token)]);
    }

    private function priority(T $token) : int
    {
        return self::PRIORITY[get_class($token)];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\AType;
use PhpStyler\Token\TArray;
use PhpStyler\Token\TBool;
use PhpStyler\Token\TFalse;
use PhpStyler\Token\TFloat;
use PhpStyler\Token\TInt;
use PhpStyler\Token\TNull;
use PhpStyler\Token\TNullable;
use PhpStyler\Token\TObject;
use PhpStyler\Token\TString;
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnion;

class OrderTypes extends ATokenRule
{
    /** @var array<class-string<AToken>, int> */
    private array $priorityMap;

    private int $wildcardPriority;

    /**
     * @param array<int, class-string<AToken>|'*'> $order
     */
    public function __construct(
        array $order = [
            TNull::class,
            TBool::class,
            TTrue::class,
            TFalse::class,
            TInt::class,
            TFloat::class,
            TString::class,
            TArray::class,
            TObject::class,
            '*',
        ],
    ) {
        $this->priorityMap = [];
        $this->wildcardPriority = PHP_INT_MAX;

        foreach ($order as $position => $entry) {
            if ($entry === '*') {
                $this->wildcardPriority = $position;
            } else {
                $this->priorityMap[$entry] = $position;
            }
        }
    }

    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            if (! $this->isTypeToken($token)) {
                $result[] = $token;
                $i ++;
                continue;
            }

            // collect union group: type [TUnion type]*
            $types = [$token];
            $i ++;

            while (
                $i < $count
                && isset($tokens[$i + 1])
                && $tokens[$i] instanceof TUnion
                && $this->isTypeToken($tokens[$i + 1])
            ) {
                $i ++; // skip TUnion
                $types[] = $tokens[$i];
                $i ++;
            }

            if (count($types) <= 1) {
                $result[] = $types[0];
                continue;
            }

            $sorted = $this->sortTypes($types);
            $this->emitSortedTypes($result, $sorted);
        }

        return $result;
    }

    /**
     * Stable-sort types by priority.
     *
     * @param AToken[] $types
     * @return AToken[]
     */
    private function sortTypes(array $types) : array
    {
        $indexed = [];

        foreach ($types as $idx => $type) {
            $indexed[] = [$type, $idx];
        }

        usort(
            $indexed,
            fn (array $a, array $b) : int
                => $this->priority($a[0]) <=> $this->priority($b[0])
                    ?: $a[1] <=> $b[1],
        );

        return array_column($indexed, 0);
    }

    /**
     * Emit sorted types, using nullable shorthand (?Type) when
     * the result is exactly [null, OtherType].
     *
     * @param AToken[] $result
     * @param AToken[] $sorted
     */
    private function emitSortedTypes(array &$result, array $sorted) : void
    {
        if ($this->isNullableShorthand($sorted)) {
            $other = $sorted[1];
            $result[] = new TNullable(ord('?'), '?', $other->line, $other->pos);
            $result[] = $other;
            return;
        }

        foreach ($sorted as $idx => $type) {
            if ($idx > 0) {
                $result[] = new TUnion(ord('|'), '|', $type->line, $type->pos);
            }

            $result[] = $type;
        }
    }

    /**
     * Check if the sorted union qualifies for ?Type shorthand:
     * exactly 2 types with null first and null prioritized before wildcard.
     *
     * @param AToken[] $sorted
     */
    private function isNullableShorthand(array $sorted) : bool
    {
        return count($sorted) === 2
            && $sorted[0] instanceof TNull
            && ($this->priorityMap[TNull::class] ?? PHP_INT_MAX)
                < $this->wildcardPriority;
    }

    private function isTypeToken(AToken $token) : bool
    {
        return $token instanceof AType;
    }

    private function priority(AToken $token) : int
    {
        return $this->priorityMap[get_class($token)] ?? $this->wildcardPriority;
    }
}

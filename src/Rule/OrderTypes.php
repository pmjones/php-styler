<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TArray;
use PhpStyler\Token\TBool;
use PhpStyler\Token\TCallable;
use PhpStyler\Token\TFalse;
use PhpStyler\Token\TFloat;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TInt;
use PhpStyler\Token\TIterable;
use PhpStyler\Token\TMixed;
use PhpStyler\Token\TNever;
use PhpStyler\Token\TNull;
use PhpStyler\Token\TNullable;
use PhpStyler\Token\TObject;
use PhpStyler\Token\TParent;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TRelativeName;
use PhpStyler\Token\TSelf;
use PhpStyler\Token\TStaticType;
use PhpStyler\Token\TString;
use PhpStyler\Token\TTrue;
use PhpStyler\Token\TUnion;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TVoid;

class OrderTypes implements TokenRule
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
     * @var array<class-string<AToken>, true>
     */
    private const TYPE_TOKENS = [
        TInt::class => true,
        TFloat::class => true,
        TBool::class => true,
        TVoid::class => true,
        TNever::class => true,
        TMixed::class => true,
        TIterable::class => true,
        TObject::class => true,
        TTrue::class => true,
        TFalse::class => true,
        TNull::class => true,
        TCallable::class => true,
        TString::class => true,
        TArray::class => true,
        TStaticType::class => true,
        TUnqualifiedName::class => true,
        TQualifiedName::class => true,
        TFullyQualifiedName::class => true,
        TRelativeName::class => true,
        TSelf::class => true,
        TParent::class => true,
    ];

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
            $types = [];
            $types[] = $token;
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
                // single type, no union — pass through
                $result[] = $types[0];
                continue;
            }

            // stable sort by priority
            $indexed = [];

            foreach ($types as $idx => $type) {
                $indexed[] = [$type, $idx];
            }

            usort(
                $indexed,
                function (array $a, array $b) : int {
                    return $this->priority($a[0]) <=> $this->priority($b[0])
                        ?: $a[1] <=> $b[1];
                },
            );

            $sorted = array_column($indexed, 0);

            // nullable shorthand: exactly 2 types with null first after sort
            if (
                (
                    $this->priorityMap[TNull::class] ?? PHP_INT_MAX
                ) < $this->wildcardPriority
                && count($sorted) === 2
                && $sorted[0] instanceof TNull
            ) {
                $other = $sorted[1];
                $result[] = new TNullable(ord('?'), '?', $other->line, $other->pos);
                $result[] = $other;
                continue;
            }

            // emit sorted types with TUnion separators
            foreach ($sorted as $idx => $type) {
                if ($idx > 0) {
                    $result[] = new TUnion(ord('|'), '|', $type->line, $type->pos);
                }

                $result[] = $type;
            }
        }

        return $result;
    }

    private function isTypeToken(AToken $token) : bool
    {
        return isset(self::TYPE_TOKENS[get_class($token)]);
    }

    private function priority(AToken $token) : int
    {
        return $this->priorityMap[get_class($token)] ?? $this->wildcardPriority;
    }
}

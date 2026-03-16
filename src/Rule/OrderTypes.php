<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TArray;
use PhpStyler\Token\TBool;
use PhpStyler\Token\TBoolean;
use PhpStyler\Token\TCallable;
use PhpStyler\Token\TDouble;
use PhpStyler\Token\TFalse;
use PhpStyler\Token\TFloat;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TInt;
use PhpStyler\Token\TInteger;
use PhpStyler\Token\TIterable;
use PhpStyler\Token\TMixed;
use PhpStyler\Token\TNever;
use PhpStyler\Token\TNull;
use PhpStyler\Token\TNullable;
use PhpStyler\Token\TObject;
use PhpStyler\Token\TParent;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TReal;
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
    /**
     * @var array<class-string<T>, int>
     */
    private const PRIORITY = [
        TNull::class => 1,
        TBool::class => 2,
        TBoolean::class => 2,
        TTrue::class => 2,
        TFalse::class => 2,
        TInt::class => 3,
        TInteger::class => 3,
        TFloat::class => 4,
        TDouble::class => 4,
        TReal::class => 4,
        TString::class => 5,
        TArray::class => 6,
        TObject::class => 7,
    ];

    /**
     * @var array<class-string<T>, true>
     */
    private const TYPE_TOKENS = [
        TInt::class => true,
        TInteger::class => true,
        TFloat::class => true,
        TDouble::class => true,
        TReal::class => true,
        TBool::class => true,
        TBoolean::class => true,
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
                fn (array $a, array $b)
                    => $this->priority($a[0]) <=> $this->priority($b[0])
                        ?: $a[1] <=> $b[1],
            );

            $sorted = array_column($indexed, 0);

            // nullable shorthand: exactly 2 types with null first after sort
            if (count($sorted) === 2 && $sorted[0] instanceof TNull) {
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

    private function isTypeToken(T $token) : bool
    {
        return isset(self::TYPE_TOKENS[get_class($token)]);
    }

    private function priority(T $token) : int
    {
        return self::PRIORITY[get_class($token)] ?? PHP_INT_MAX;
    }
}

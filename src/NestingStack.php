<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AFnNesting;
use PhpStyler\Token\AnEncapsedStringOpening;
use PhpStyler\Token\ATernaryNesting;
use PhpStyler\Token\AToken;

class NestingStack
{
    /** @var array<int, Nesting> */
    private array $nesting = [];

    public function push(AToken $token) : void
    {
        $this->nesting[] = new Nesting($token);
    }

    public function pop() : ?AToken
    {
        $nesting = array_pop($this->nesting);

        return $nesting->token ?? null;
    }

    /**
     * @param class-string $kind
     */
    public function at(string $kind, string ...$kinds) : bool
    {
        array_unshift($kinds, $kind);
        $nestingOffset = count($this->nesting);

        foreach ($kinds as $kind) {
            $nestingOffset --;
            $nesting = $this->nesting[$nestingOffset] ?? null;

            if (! $nesting?->token instanceof $kind) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return class-string
     */
    public function getClass() : string
    {
        $nesting = end($this->nesting);

        /** @var class-string */
        return $nesting !== false ? $nesting->class : '';
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getOpeningBrace() : ?string
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->openingBrace : null;
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getClosingBrace() : ?string
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->closingBrace : null;
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getEndSemicolon() : ?string
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->endSemicolon : null;
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getClosingBraceless() : ?string
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->closingBraceless : null;
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getContinuationBraceless() : ?string
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->continuationBraceless : null;
    }

    public function getArgCount() : int
    {
        $nesting = end($this->nesting);
        return $nesting !== false ? $nesting->argCount : 0;
    }

    public function incrementArgCount() : void
    {
        if ($this->nesting !== []) {
            end($this->nesting)->argCount ++;
        }
    }

    /**
     * @return array<int, class-string>
     */
    public function listAll() : array
    {
        /** @var array<int, class-string> */
        return array_map(fn (Nesting $n) => $n->class, $this->nesting);
    }

    public function inEncapsedString() : bool
    {
        $nesting = end($this->nesting);

        return $nesting !== false
            && $nesting->token instanceof AnEncapsedStringOpening;
    }

    public function popTernary() : void
    {
        while ($this->nesting !== []) {
            $token = end($this->nesting)->token;

            if ($token instanceof ATernaryNesting) {
                array_pop($this->nesting);
            } elseif ($token instanceof AFnNesting) {
                array_pop($this->nesting); // TFnDoubleArrow
                array_pop($this->nesting); // TFn
            } else {
                break;
            }
        }
    }
}

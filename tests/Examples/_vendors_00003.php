<?php
for ($i = count($this->tokens) - 1; $i >= 0; $i --) {
    if (
        ! $this->tokens[$i] instanceof LongTypeName
        && ! $this->tokens[$i] instanceof AnotherLongTypeName
    ) {
        return $i;
    }
}

if (true) {
    if (true) {
        if (true) {
            if (
                $firstSplit->shouldSkipFirst(count($group['positions']))
                || ($tokens[$firstPos + 1] ?? null) instanceof TMemberDoubleColon
            ) {
                array_shift($group['positions']);
            }
        }
    }
}

class FooFactory
{
    public function new() : self
    {
    }

    protected function print(string $source) : string
    {
    }
}

// should not break at operator when it's the only operator
// -            TConst::class => $parser->atNesting(TConst::class, TClasslikeOpeningBrace::class)
// +            TConst::class => $parser
// +                ->atNesting(TConst::class, TClasslikeOpeningBrace::class)
//                  ? TConstEndSemicolon::class
//                  : TNamespaceConstEndSemicolon::class,

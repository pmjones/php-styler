<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserBranchesTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'if-braceless' => [
                <<<'CODE'
                <?php
                if ($a) echo 'a';
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    echo 'a';
                }

                EXPECT,
            ],
            'if-else-braceless' => [
                <<<'CODE'
                <?php
                if ($a) echo 'a';
                else echo 'b';
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    echo 'a';
                } else {
                    echo 'b';
                }

                EXPECT,
            ],
            'if-elseif-braceless' => [
                <<<'CODE'
                <?php
                if ($a) echo 'a';
                elseif ($b) echo 'b';
                else echo 'c';
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    echo 'a';
                } elseif ($b) {
                    echo 'b';
                } else {
                    echo 'c';
                }

                EXPECT,
            ],
            'for-braceless' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 10; $i++) echo $i;
                CODE,
                <<<'EXPECT'
                <?php
                for ($i = 0; $i < 10; $i ++) {
                    echo $i;
                }

                EXPECT,
            ],
            'foreach-braceless' => [
                <<<'CODE'
                <?php
                foreach ($items as $item) echo $item;
                CODE,
                <<<'EXPECT'
                <?php
                foreach ($items as $item) {
                    echo $item;
                }

                EXPECT,
            ],
            'while-braceless' => [
                <<<'CODE'
                <?php
                while ($x < 10) $x++;
                CODE,
                <<<'EXPECT'
                <?php
                while ($x < 10) {
                    $x ++;
                }

                EXPECT,
            ],
            'do-while-braceless' => [
                <<<'CODE'
                <?php
                do $x++; while ($x < 10);
                CODE,
                <<<'EXPECT'
                <?php
                do $x ++;
                while ($x < 10);

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-if' => [
                <<<'CODE'
                <?php
                while ($a) if ($b) { echo 'x'; }
                CODE,
                <<<'EXPECT'
                <?php
                while ($a) {
                    if ($b) {
                        echo 'x';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-for' => [
                <<<'CODE'
                <?php
                if ($a) for ($i = 0; $i < 3; $i++) { echo $i; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    for ($i = 0; $i < 3; $i ++) {
                        echo $i;
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-foreach' => [
                <<<'CODE'
                <?php
                if ($a) foreach ($items as $item) { echo $item; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foreach ($items as $item) {
                        echo $item;
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-while' => [
                <<<'CODE'
                <?php
                if ($a) while ($b) { echo 'y'; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    while ($b) {
                        echo 'y';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-switch' => [
                <<<'CODE'
                <?php
                if ($a) switch ($b) { case 1: echo 'one'; break; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    switch ($b) {
                        case 1:
                            echo 'one';
                            break;
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-else' => [
                <<<'CODE'
                <?php
                if ($a) if ($b) { echo 'yes'; } else { echo 'no'; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    if ($b) {
                        echo 'yes';
                    } else {
                        echo 'no';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-elseif' => [
                <<<'CODE'
                <?php
                if ($a) if ($b) { echo 'b'; } elseif ($c) { echo 'c'; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    if ($b) {
                        echo 'b';
                    } elseif ($c) {
                        echo 'c';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-try-finally' => [
                <<<'CODE'
                <?php
                if ($a) try { echo 'a'; } catch (\Exception $e) { } finally { echo 'b'; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    try {
                        echo 'a';
                    } catch (\Exception $e) {
                    } finally {
                        echo 'b';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-inner-declare' => [
                <<<'CODE'
                <?php
                if ($a) declare(ticks=1) { echo 'a'; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    declare(ticks=1) {
                        echo 'a';
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-function-def' => [
                <<<'CODE'
                <?php
                if ($a) function foo() { echo 1; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    function foo()
                    {
                        echo 1;
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-empty-switch-body' => [
                <<<'CODE'
                <?php
                if ($a) switch ($b) { }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    switch ($b) {
                    }
                }

                EXPECT,
            ],
            'nested-braceless-outer-braced-full-switch' => [
                <<<'CODE'
                <?php
                if ($a) switch ($b) { case 1: break; default: break; }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    switch ($b) {
                        case 1:
                            break;
                        default:
                            break;
                    }
                }

                EXPECT,
            ],
            'for-empty-condition-braceless' => [
                <<<'CODE'
                <?php
                for (;;) echo 1;
                CODE,
                <<<'EXPECT'
                <?php
                for (; ; ) {
                    echo 1;
                }

                EXPECT,
            ],
            'php-closing-tag-with-trailing-html' => [
                <<<'CODE'
                <?php
                echo 1;
                ?>trailing html<?php
                echo 2;
                CODE,
                <<<'EXPECT'
                <?php
                echo 1;
                ?>trailing html<?php
                 echo 2;

                EXPECT,
            ],
            'match-statement-position' => [
                <<<'CODE'
                <?php
                match ($x) { 1 => 'a', 2 => 'b', default => 'c' };
                CODE,
                <<<'EXPECT'
                <?php
                match ($x) {
                    1 => 'a',
                    2 => 'b',
                    default => 'c'
                };

                EXPECT,
            ],
            'multi-attribute-normalizes-to-separate-lines' => [
                <<<'CODE'
                <?php
                #[A, B, C]
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[A]
                #[B]
                #[C]
                function foo()
                {
                }

                EXPECT,
            ],
            'multi-inline-attribute-parameter' => [
                <<<'CODE'
                <?php
                function foo(#[A, B] $bar) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(#[A] #[B] $bar)
                {
                }

                EXPECT,
            ],
            'trailing-inline-comment-doc' => [
                <<<'CODE'
                <?php
                if ($x) { echo 1; } /** trail */
                $y = 2;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    echo 1;
                } /** trail */

                $y = 2;

                EXPECT,
            ],
            'trailing-inline-comment-slash' => [
                <<<'CODE'
                <?php
                if ($x) { echo 1; } // trail
                $y = 2;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    echo 1;
                } // trail

                $y = 2;

                EXPECT,
            ],
            'trailing-inline-comment-hash' => [
                <<<'CODE'
                <?php
                if ($x) { echo 1; } # trail
                $y = 2;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    echo 1;
                } # trail

                $y = 2;

                EXPECT,
            ],
            'class-brace-then-doc-comment' => [
                <<<'CODE'
                <?php
                class Foo { /** doc */
                public $x = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo{
                    /** doc */
                    public $x = 1;
                }

                EXPECT,
            ],
            'class-brace-then-slashed-comment' => [
                <<<'CODE'
                <?php
                class Foo { // slash
                public $x = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo{
                    // slash
                    public $x = 1;
                }

                EXPECT,
            ],
            'class-brace-then-hashed-comment' => [
                <<<'CODE'
                <?php
                class Foo { # hash
                public $x = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo{
                    # hash
                    public $x = 1;
                }

                EXPECT,
            ],
            'blank-line-before-doc-comment' => [
                <<<'CODE'
                <?php

                /** doc */
                $x = 1;
                CODE,
                <<<'EXPECT'
                <?php

                /** doc */
                $x = 1;

                EXPECT,
            ],
            'blank-line-before-slashed-comment' => [
                <<<'CODE'
                <?php

                // comment
                $x = 1;
                CODE,
                <<<'EXPECT'
                <?php

                // comment
                $x = 1;

                EXPECT,
            ],
            'blank-line-before-hashed-comment' => [
                <<<'CODE'
                <?php

                # comment
                $x = 1;
                CODE,
                <<<'EXPECT'
                <?php

                # comment
                $x = 1;

                EXPECT,
            ],
            'function-named-new' => [
                <<<'CODE'
                <?php
                class Foo {
                    public function new() : self {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function new() : self
                    {
                    }
                }

                EXPECT,
            ],
            'function-named-print' => [
                <<<'CODE'
                <?php
                class Foo {
                    public function print() : void {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function print() : void
                    {
                    }
                }

                EXPECT,
            ],
            'static-function-named-print' => [
                <<<'CODE'
                <?php
                class Foo {
                    public static function print() : void {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public static function print() : void
                    {
                    }
                }

                EXPECT,
            ],
            'abstract-magic-method-with-member-spacing' => [
                <<<'CODE'
                <?php
                abstract class Foo {
                    public string $x = '';
                    abstract public function __toString() : string;
                }
                CODE,
                <<<'EXPECT'
                <?php
                abstract class Foo
                {
                    public string $x = '';

                    abstract public function __toString() : string;
                }

                EXPECT,
            ],
            'property-hooks-with-member-spacing' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $x = '';
                    public string $y { get => $this->x; set => $this->x = $value; }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public string $x = '';

                    public string $y {
                        get => $this->x;
                        set => $this->x = $value;
                    }
                }

                EXPECT,
            ],
            'abstract-property-hooks-with-member-spacing' => [
                <<<'CODE'
                <?php
                abstract class Foo {
                    public string $x = '';
                    abstract public string $y { get; set; }
                }
                CODE,
                <<<'EXPECT'
                <?php
                abstract class Foo
                {
                    public string $x = '';

                    abstract public string $y { get; set; }
                }

                EXPECT,
            ],
            'inline-attribute-splits-to-own-lines' => [
                <<<'CODE'
                <?php
                function foo(#[ReallyLongFooAttributeName, ReallyLongBarAttributeName, AnotherLongOneHere] $someReallyLongParamName) {}
                CODE,
                <<<'EXPECT'
                <?php

                function foo(
                    #[ReallyLongFooAttributeName]
                    #[ReallyLongBarAttributeName]
                    #[AnotherLongOneHere]
                    $someReallyLongParamName
                )
                {
                }

                EXPECT,
            ],
            'multi-inline-attribute-long-split' => [
                <<<'CODE'
                <?php
                function verylongFunctionName(#[Foo, Bar] int $firstparameterVeryLong, #[Baz] string $second) {}
                CODE,
                <<<'EXPECT'
                <?php

                function verylongFunctionName(
                    #[Foo] #[Bar] int $firstparameterVeryLong,
                    #[Baz] string $second
                )
                {
                }

                EXPECT,
            ],
            'trailing-comma-in-attribute-list' => [
                <<<'CODE'
                <?php
                #[A, B,]
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[A]
                #[B]
                function foo()
                {
                }

                EXPECT,
            ],
            'function-name-new-returns-by-reference' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function &new() : self
                    {
                        return $this;
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function &new() : self
                    {
                        return $this;
                    }
                }

                EXPECT,
            ],
            'function-name-print-returns-by-reference' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function &print() : self
                    {
                        return $this;
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function &print() : self
                    {
                        return $this;
                    }
                }

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [RemoveTrailingBlankLines::class]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

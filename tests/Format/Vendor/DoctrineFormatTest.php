<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class DoctrineFormatTest extends TestCase
{
    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(new DoctrineFormat());
    }

    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $actual = ($this->styler)($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'space-after-not' => [
                <<<'CODE'
                <?php
                $x = !$y;
                CODE,
                <<<'EXPECT'
                <?php
                $x = ! $y;

                EXPECT,
            ],
            'space-after-cast' => [
                <<<'CODE'
                <?php
                $x = (int)$y;
                CODE,
                <<<'EXPECT'
                <?php
                $x = (int) $y;

                EXPECT,
            ],
            'no-space-around-increment' => [
                <<<'CODE'
                <?php
                $i++;
                CODE,
                <<<'EXPECT'
                <?php
                $i++;

                EXPECT,
            ],
            'single-quotes' => [
                <<<'CODE'
                <?php
                $x = "hello";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'hello';

                EXPECT,
            ],
            'instantiation-parens' => [
                <<<'CODE'
                <?php
                $x = new Foo;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'concatenation-spacing' => [
                <<<'CODE'
                <?php
                $x = $a.$b;
                CODE,
                <<<'EXPECT'
                <?php
                $x = $a . $b;

                EXPECT,
            ],
            'null-last-in-return-type' => [
                <<<'CODE'
                <?php
                function foo(): null|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): int|null
                {
                }

                EXPECT,
            ],
            'null-last-not-nullable-shorthand' => [
                <<<'CODE'
                <?php
                function foo(): null|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): string|null
                {
                }

                EXPECT,
            ],
            'lowercase-function-call' => [
                <<<'CODE'
                <?php
                $x = Array_Map('strtolower', $a);
                CODE,
                <<<'EXPECT'
                <?php
                $x = array_map('strtolower', $a);

                EXPECT,
            ],
            'blank-line-before-return' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $x = 1;
                    return $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $x = 1;

                    return $x;
                }

                EXPECT,
            ],
            'no-blank-line-before-return-first-statement' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    return 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return 1;
                }

                EXPECT,
            ],
            'blank-line-before-throw' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $x = 1;
                    throw new Exception();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $x = 1;

                    throw new Exception();
                }

                EXPECT,
            ],
            'blank-line-after-if-block' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }

                    baz();
                }

                EXPECT,
            ],
            'blank-line-after-do-while' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    do {
                        bar();
                    } while ($x);
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    do {
                        bar();
                    } while ($x);

                    baz();
                }

                EXPECT,
            ],
            'no-blank-line-after-block-last-statement' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                }

                EXPECT,
            ],
            'no-blank-line-between-if-else' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    } else {
                        baz();
                    }
                    qux();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    } else {
                        baz();
                    }

                    qux();
                }

                EXPECT,
            ],
            'no-blank-line-between-try-catch-finally' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    try {
                        bar();
                    } catch (Exception $e) {
                        baz();
                    } finally {
                        qux();
                    }
                    done();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    try {
                        bar();
                    } catch (Exception $e) {
                        baz();
                    } finally {
                        qux();
                    }

                    done();
                }

                EXPECT,
            ],
            'split-joined-attributes' => [
                <<<'CODE'
                <?php
                #[Foo, Bar]
                class Baz {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                #[Bar]
                class Baz
                {
                }

                EXPECT,
            ],
            'split-joined-attributes-with-args' => [
                <<<'CODE'
                <?php
                #[Foo(1, 2), Bar]
                class Baz {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo(1, 2)]
                #[Bar]
                class Baz
                {
                }

                EXPECT,
            ],
            'member-spacing-constants-grouped' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    const B = 2;
                    public $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;

                    public $x;
                }

                EXPECT,
            ],
            'member-spacing-properties-grouped' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                    public $y;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'member-spacing-methods-separated' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public function baz() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
        ];
    }
}

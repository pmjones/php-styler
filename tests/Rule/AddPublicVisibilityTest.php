<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class AddPublicVisibilityTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(eol: "\n", rules: [new AddPublicVisibility()]);
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'method-no-visibility' => [
                <<<'CODE'
                <?php class Foo { function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'const-no-visibility' => [
                <<<'CODE'
                <?php class Foo { const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public const BAR = 1;
                }

                EXPECT,
            ],
            'method-already-public' => [
                <<<'CODE'
                <?php class Foo { public function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'method-already-protected' => [
                <<<'CODE'
                <?php class Foo { protected function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    protected function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'method-with-static-no-visibility' => [
                <<<'CODE'
                <?php class Foo { static function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    static public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'abstract-method-no-visibility' => [
                <<<'CODE'
                <?php abstract class Foo { abstract function bar(); }
                CODE,
                <<<'EXPECT'
                <?php abstract class Foo
                {
                    abstract public function bar();
                }

                EXPECT,
            ],
            'final-const-no-visibility' => [
                <<<'CODE'
                <?php class Foo { final const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    final public const BAR = 1;
                }

                EXPECT,
            ],
            'top-level-function-unchanged' => [
                <<<'CODE'
                <?php function bar() {}
                CODE,
                <<<'EXPECT'
                <?php function bar()
                {
                }

                EXPECT,
            ],
            'interface-method' => [
                <<<'CODE'
                <?php interface Foo { function bar(); }
                CODE,
                <<<'EXPECT'
                <?php interface Foo
                {
                    public function bar();
                }

                EXPECT,
            ],
            'trait-method' => [
                <<<'CODE'
                <?php trait Foo { function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php trait Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'enum-method' => [
                <<<'CODE'
                <?php enum Foo { function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php enum Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'enum-const' => [
                <<<'CODE'
                <?php enum Foo { const BAR = 1; }
                CODE,
                <<<'EXPECT'
                <?php enum Foo
                {
                    public const BAR = 1;
                }

                EXPECT,
            ],
            'closure-inside-method' => [
                <<<'CODE'
                <?php class Foo { public function bar() { $fn = function() {}; } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function bar()
                    {
                        $fn = function () {
                        };
                    }
                }

                EXPECT,
            ],
            'anonymous-class-method' => [
                <<<'CODE'
                <?php $x = new class { function bar() {} };
                CODE,
                <<<'EXPECT'
                <?php $x = new class {
                    public function bar()
                    {
                    }
                };

                EXPECT,
            ],
        ];
    }
}

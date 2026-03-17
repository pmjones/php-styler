<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class OrderModifiersTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new Format(rules: [new OrderModifiers(), new RemoveTrailingBlankLines()]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'already-correct-order' => [
                <<<'CODE'
                <?php class Foo { public static function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'reversed-visibility-static' => [
                <<<'CODE'
                <?php class Foo { static public function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'readonly-before-visibility' => [
                <<<'CODE'
                <?php class Foo { readonly public string $bar; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public readonly string $bar;
                }

                EXPECT,
            ],
            'static-before-visibility-method' => [
                <<<'CODE'
                <?php class Foo { static protected function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    protected static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'abstract-after-visibility' => [
                <<<'CODE'
                <?php abstract class Foo { public abstract function bar(); }
                CODE,
                <<<'EXPECT'
                <?php abstract class Foo
                {
                    abstract public function bar();
                }

                EXPECT,
            ],
            'final-after-visibility' => [
                <<<'CODE'
                <?php class Foo { public final function bar() {} }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    final public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'var-to-public' => [
                <<<'CODE'
                <?php class Foo { var $bar; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public $bar;
                }

                EXPECT,
            ],
            'var-with-other-modifiers' => [
                <<<'CODE'
                <?php class Foo { var static $bar; }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public static $bar;
                }

                EXPECT,
            ],
            'static-not-affected-when-alone' => [
                <<<'CODE'
                <?php class Foo { public function bar() { static $x = 1; } }
                CODE,
                <<<'EXPECT'
                <?php class Foo
                {
                    public function bar()
                    {
                        static $x = 1;
                    }
                }

                EXPECT,
            ],
        ];
    }
}

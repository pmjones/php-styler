<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class AddInstantiationParenthesesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                AddInstantiationParentheses::class,
                RemoveTrailingBlankLines::class,
            ]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'missing-parens' => [
                <<<'CODE'
                <?php
                $x = new Foo;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'already-has-parens' => [
                <<<'CODE'
                <?php
                $x = new Foo();
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'with-args' => [
                <<<'CODE'
                <?php
                $x = new Foo($a);
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo($a);

                EXPECT,
            ],
            'qualified-name' => [
                <<<'CODE'
                <?php
                $x = new Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo\Bar();

                EXPECT,
            ],
            'fully-qualified' => [
                <<<'CODE'
                <?php
                $x = new \Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new \Foo\Bar();

                EXPECT,
            ],
            'variable-class' => [
                <<<'CODE'
                <?php
                $x = new $class;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new $class();

                EXPECT,
            ],
            'anonymous-class-skip' => [
                <<<'CODE'
                <?php
                $x = new class {
                    public function foo() {}
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                    public function foo()
                    {
                    }
                };

                EXPECT,
            ],
        ];
    }
}

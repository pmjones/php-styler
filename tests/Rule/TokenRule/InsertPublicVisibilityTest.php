<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InsertPublicVisibilityTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                InsertPublicVisibility::class,
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
            'function-in-class' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'const-in-class' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                }

                EXPECT,
            ],
            'already-visible' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    protected function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    protected function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'static-function' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    static function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'nested-function-in-method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar()
                    {
                        function nested() {}
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                        function nested()
                        {
                        }
                    }
                }

                EXPECT,
            ],
            'nested-function-in-anonymous-function' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar()
                    {
                        $fn = function () {
                            function nested() {}
                        };
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                        $fn = function () {
                            function nested()
                            {
                            }
                        };
                    }
                }

                EXPECT,
            ],
            'standalone-function' => [
                <<<'CODE'
                <?php
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                }

                EXPECT,
            ],
        ];
    }
}

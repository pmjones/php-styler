<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Percs30FormatTest extends TestCase
{
    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(new Percs30Format());
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $actual = ($this->styler)($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'return-type-colon-spacing' => [
                <<<'CODE'
                <?php
                function foo() : int
                {
                    return 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): int
                {
                    return 1;
                }

                EXPECT,
            ],
            'class-brace-next-line' => [
                <<<'CODE'
                <?php
                class Foo {
                    public $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                }

                EXPECT,
            ],
            'function-brace-next-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar() {}
                }

                EXPECT,
            ],
            'control-brace-same-line' => [
                <<<'CODE'
                <?php
                if ($x)
                {
                    bar();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    bar();
                }

                EXPECT,
            ],
            'else-same-line' => [
                <<<'CODE'
                <?php
                if ($x)
                {
                    bar();
                }
                else
                {
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    bar();
                } else {
                    baz();
                }

                EXPECT,
            ],
            'trailing-comma-added-multiline' => [
                <<<'CODE'
                <?php
                function foo(
                    int $aLongParameterName,
                    int $anotherLongParameterName,
                    int $yetAnotherLongParameterName,
                    int $andOneMoreLongParameterName
                ) {
                }
                CODE,
                <<<'EXPECT'
                <?php

                function foo(
                    int $aLongParameterName,
                    int $anotherLongParameterName,
                    int $yetAnotherLongParameterName,
                    int $andOneMoreLongParameterName,
                ) {}

                EXPECT,
            ],
            'trailing-comma-removed-singleline' => [
                <<<'CODE'
                <?php
                foo($x, $y,);
                CODE,
                <<<'EXPECT'
                <?php
                foo($x, $y);

                EXPECT,
            ],
            'elseif-conversion' => [
                <<<'CODE'
                <?php
                if ($a) {
                    foo();
                } else if ($b) {
                    bar();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                }

                EXPECT,
            ],
            'remove-php-closing-tag' => [
                <<<'CODE'
                <?php
                $x = 1;
                ?>
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
            'php-opening-tag-newline' => [
                <<<'CODE'
                <?php $x = 1;
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
            'declare-no-spaces' => [
                <<<'CODE'
                <?php
                declare(strict_types = 1);
                CODE,
                <<<'EXPECT'
                <?php
                declare(strict_types=1);

                EXPECT,
            ],
            'expand-trait-use' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar, Baz;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use Bar;
                    use Baz;
                }

                EXPECT,
            ],
            'trait-use-blank-line-before-members' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar;
                    public $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use Bar;

                    public $x;
                }

                EXPECT,
            ],
            'exit-parens' => [
                <<<'CODE'
                <?php
                exit;
                CODE,
                <<<'EXPECT'
                <?php
                exit();

                EXPECT,
            ],
            'remove-empty-anon-class-parens' => [
                <<<'CODE'
                <?php
                $x = new class() {
                    public function foo() {}
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                    public function foo() {}
                };

                EXPECT,
            ],
            'remove-empty-attribute-parens' => [
                <<<'CODE'
                <?php
                #[Override()]
                function foo() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Override]
                function foo() {}

                EXPECT,
            ],
            ...(PHP_VERSION_ID >= 80400 ? [
                'set-visibility-ordering' => [
                    <<<'CODE'
                    <?php
                    class Foo
                    {
                        static public protected(set) string $bar = '';
                    }
                    CODE,
                    <<<'EXPECT'
                    <?php
                    class Foo
                    {
                        public protected(set) static string $bar = '';
                    }

                    EXPECT,
                ],
            ] : []),
            'heredoc-to-nowdoc' => [
                <<<'CODE'
                <?php
                $x = <<<EOT
                hello world
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<'EOT'
                hello world
                EOT;

                EXPECT,
            ],
            'import-leading-backslash-removed' => [
                <<<'CODE'
                <?php
                use \Foo\Bar;
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                new Bar();

                EXPECT,
            ],
            'empty-function-collapsed' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar() {}
                }

                EXPECT,
            ],
            'empty-class-collapsed' => [
                <<<'CODE'
                <?php
                class Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo {}

                EXPECT,
            ],
            'empty-closure-collapsed' => [
                <<<'CODE'
                <?php
                $x = function () {
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = function () {};

                EXPECT,
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PhpStyler\TestFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeImportsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            // --- ordering tests (from OrderImportsTest) ---
            'mixed-imports' => [
                <<<'CODE'
                <?php
                use function Foo\bar;
                use Baz\Qux;
                use const Foo\BAR;
                use Alpha\Beta;
                use function Alpha\gamma;
                use const Zed\THING;

                new Qux();
                new Beta();
                bar();
                gamma();
                echo BAR;
                echo THING;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use const Foo\BAR;
                use const Zed\THING;

                use function Alpha\gamma;
                use function Foo\bar;

                new Qux();
                new Beta();
                bar();
                gamma();
                echo BAR;
                echo THING;

                EXPECT,
            ],
            'already-grouped' => [
                <<<'CODE'
                <?php
                use Alpha\Beta;
                use Baz\Qux;
                use const Foo\BAR;
                use function Foo\bar;

                new Beta();
                new Qux();
                echo BAR;
                bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use const Foo\BAR;

                use function Foo\bar;

                new Beta();
                new Qux();
                echo BAR;
                bar();

                EXPECT,
            ],
            'only-classlikes' => [
                <<<'CODE'
                <?php
                use Zed\Omega;
                use Alpha\Beta;
                use Foo\Bar;

                new Omega();
                new Beta();
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Foo\Bar;
                use Zed\Omega;

                new Omega();
                new Beta();
                new Bar();

                EXPECT,
            ],
            'class-and-function' => [
                <<<'CODE'
                <?php
                use function Foo\bar;
                use Alpha\Beta;
                use function Alpha\gamma;
                use Baz\Qux;

                new Beta();
                new Qux();
                bar();
                gamma();
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use function Alpha\gamma;
                use function Foo\bar;

                new Beta();
                new Qux();
                bar();
                gamma();

                EXPECT,
            ],
            'imports-before-class' => [
                <<<'CODE'
                <?php
                use Baz\Qux;
                use Alpha\Beta;
                class Foo extends Beta implements Qux {}
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                class Foo extends Beta implements Qux
                {
                }

                EXPECT,
            ],
            'alphabetize-within-kind' => [
                <<<'CODE'
                <?php
                use Zed\Omega;
                use alpha\beta;
                use Foo\Bar;

                new Omega();
                new beta();
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use alpha\beta;
                use Foo\Bar;
                use Zed\Omega;

                new Omega();
                new beta();
                new Bar();

                EXPECT,
            ],

            // --- remove-unused tests (from RemoveUnusedImportsTest) ---
            'classlike-used-in-code' => [
                <<<'CODE'
                <?php
                use Foo\Bar;

                class Baz extends Bar
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                class Baz extends Bar
                {
                }

                EXPECT,
            ],
            'classlike-unused' => [
                <<<'CODE'
                <?php
                use Foo\Bar;

                class Baz
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Baz
                {
                }

                EXPECT,
            ],
            'function-import-used' => [
                <<<'CODE'
                <?php
                use function Foo\bar;

                bar();
                CODE,
                <<<'EXPECT'
                <?php
                use function Foo\bar;

                bar();

                EXPECT,
            ],
            'function-import-unused' => [
                <<<'CODE'
                <?php
                use function Foo\bar;

                baz();
                CODE,
                <<<'EXPECT'
                <?php
                baz();

                EXPECT,
            ],
            'const-import-used' => [
                <<<'CODE'
                <?php
                use const Foo\BAR;

                echo BAR;
                CODE,
                <<<'EXPECT'
                <?php
                use const Foo\BAR;

                echo BAR;

                EXPECT,
            ],
            'const-import-unused' => [
                <<<'CODE'
                <?php
                use const Foo\BAR;

                echo "hello";
                CODE,
                <<<'EXPECT'
                <?php
                echo "hello";

                EXPECT,
            ],
            'function-import-unqualified-used' => [
                <<<'CODE'
                <?php
                use function bar;

                bar();
                CODE,
                <<<'EXPECT'
                <?php
                use function bar;

                bar();

                EXPECT,
            ],
            'const-import-unqualified-used' => [
                <<<'CODE'
                <?php
                use const BAR;

                echo BAR;
                CODE,
                <<<'EXPECT'
                <?php
                use const BAR;

                echo BAR;

                EXPECT,
            ],
            'function-import-aliased-used' => [
                <<<'CODE'
                <?php
                use function Foo\bar as baz;

                baz();
                CODE,
                <<<'EXPECT'
                <?php
                use function Foo\bar as baz;

                baz();

                EXPECT,
            ],
            'const-import-aliased-used' => [
                <<<'CODE'
                <?php
                use const Foo\BAR as BAZ;

                echo BAZ;
                CODE,
                <<<'EXPECT'
                <?php
                use const Foo\BAR as BAZ;

                echo BAZ;

                EXPECT,
            ],
            'aliased-import-used-by-alias' => [
                <<<'CODE'
                <?php
                use Foo\Bar as Baz;

                class Qux extends Baz
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar as Baz;

                class Qux extends Baz
                {
                }

                EXPECT,
            ],
            'import-used-in-docblock-param' => [
                <<<'CODE'
                <?php
                use Foo\Bar;

                class Baz
                {
                    /** @param Bar $bar */
                    public function qux($bar)
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                class Baz
                {
                    /** @param Bar $bar */
                    public function qux($bar)
                    {
                    }
                }

                EXPECT,
            ],
            'import-used-in-generic-type' => [
                <<<'CODE'
                <?php
                use Foo\Bar;

                class Baz
                {
                    /** @return array<string, Bar> */
                    public function qux()
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                class Baz
                {
                    /** @return array<string, Bar> */
                    public function qux()
                    {
                    }
                }

                EXPECT,
            ],
            'multiple-imports-some-unused' => [
                <<<'CODE'
                <?php
                use Foo\Alpha;
                use Foo\Beta;
                use Foo\Gamma;

                class Baz extends Alpha
                {
                    public function qux() : Gamma
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Alpha;
                use Foo\Gamma;

                class Baz extends Alpha
                {
                    public function qux() : Gamma
                    {
                    }
                }

                EXPECT,
            ],
            'all-imports-used' => [
                <<<'CODE'
                <?php
                use Foo\Alpha;
                use Foo\Beta;

                class Baz extends Alpha implements Beta
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Alpha;
                use Foo\Beta;

                class Baz extends Alpha implements Beta
                {
                }

                EXPECT,
            ],
            'all-imports-unused' => [
                <<<'CODE'
                <?php
                use Foo\Alpha;
                use Foo\Beta;

                class Baz
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Baz
                {
                }

                EXPECT,
            ],

            // --- closure use is not an import ---
            'closure-use-not-treated-as-import' => [
                <<<'CODE'
                <?php
                class Baz
                {
                    public function qux() : \Closure
                    {
                        $x = 1;
                        $fn = function () use ($x) : int {
                            return $x;
                        };
                        return $fn;
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Baz
                {
                    public function qux() : \Closure
                    {
                        $x = 1;
                        $fn = function () use ($x) : int {
                            return $x;
                        };
                        return $fn;
                    }
                }

                EXPECT,
            ],

            // --- combined test ---
            'remove-unused-then-order' => [
                <<<'CODE'
                <?php
                use function Zed\omega;
                use Foo\Unused;
                use const Alpha\BETA;
                use Baz\Qux;
                use function Alpha\gamma;
                use Alpha\Beta;
                use const Foo\UNUSED_CONST;

                new Qux();
                new Beta();
                gamma();
                omega();
                echo BETA;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use const Alpha\BETA;

                use function Alpha\gamma;
                use function Zed\omega;

                new Qux();
                new Beta();
                gamma();
                omega();
                echo BETA;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                NormalizeImports::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

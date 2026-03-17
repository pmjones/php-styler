<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Format;
use PhpStyler\Styler;

class RemoveUnusedImportsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(new Format(
            rules: [new ExpandImports(), new RemoveUnusedImports(), new RemoveTrailingBlankLines()],
        ));
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
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
        ];
    }
}

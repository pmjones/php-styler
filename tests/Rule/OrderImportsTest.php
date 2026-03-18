<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Styler;
use PhpStyler\TestFormat;
use PHPUnit\Framework\TestCase;

class OrderImportsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new TestFormat(rules: [
                ExpandImports::class,
                OrderImports::class,
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
            'mixed-imports' => [
                <<<'CODE'
                <?php
                use function Foo\bar;
                use Baz\Qux;
                use const Foo\BAR;
                use Alpha\Beta;
                use function Alpha\gamma;
                use const Zed\THING;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use const Foo\BAR;
                use const Zed\THING;

                use function Alpha\gamma;
                use function Foo\bar;

                EXPECT,
            ],
            'already-grouped' => [
                <<<'CODE'
                <?php
                use Alpha\Beta;
                use Baz\Qux;
                use const Foo\BAR;
                use function Foo\bar;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use const Foo\BAR;

                use function Foo\bar;

                EXPECT,
            ],
            'only-classlikes' => [
                <<<'CODE'
                <?php
                use Zed\Omega;
                use Alpha\Beta;
                use Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Foo\Bar;
                use Zed\Omega;

                EXPECT,
            ],
            'class-and-function' => [
                <<<'CODE'
                <?php
                use function Foo\bar;
                use Alpha\Beta;
                use function Alpha\gamma;
                use Baz\Qux;
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                use function Alpha\gamma;
                use function Foo\bar;

                EXPECT,
            ],
            'imports-before-class' => [
                <<<'CODE'
                <?php
                use Baz\Qux;
                use Alpha\Beta;
                class Foo {}
                CODE,
                <<<'EXPECT'
                <?php
                use Alpha\Beta;
                use Baz\Qux;

                class Foo
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
                CODE,
                <<<'EXPECT'
                <?php
                use alpha\beta;
                use Foo\Bar;
                use Zed\Omega;

                EXPECT,
            ],
        ];
    }
}

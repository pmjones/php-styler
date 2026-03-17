<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ExpandImportsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new Format(rules: [new ExpandImports(), new RemoveTrailingBlankLines()]),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic-group-use' => [
                <<<'CODE'
                <?php use Foo\{Bar, Baz};
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;
                use Foo\Baz;

                EXPECT,
            ],
            'group-use-with-alias' => [
                <<<'CODE'
                <?php use Foo\{Bar, Baz as Qux};
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;
                use Foo\Baz as Qux;

                EXPECT,
            ],
            'single-item-group' => [
                <<<'CODE'
                <?php use Foo\{Bar};
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;

                EXPECT,
            ],
            'non-grouped-use-unchanged' => [
                <<<'CODE'
                <?php use Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php use Foo\Bar;

                EXPECT,
            ],
        ];
    }
}

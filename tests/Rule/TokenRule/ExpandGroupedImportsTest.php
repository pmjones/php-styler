<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExpandGroupedImportsTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandGroupedImports::class,
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
            'basic' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, Baz};
                new Bar();
                new Baz();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                new Bar();
                new Baz();

                EXPECT,
            ],
            'with-alias' => [
                <<<'CODE'
                <?php
                use Foo\{Bar, Baz as B};
                new Bar();
                new B();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;
                use Foo\Baz as B;

                new Bar();
                new B();

                EXPECT,
            ],
            'not-grouped' => [
                <<<'CODE'
                <?php
                use Foo\Bar;
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                new Bar();

                EXPECT,
            ],
            'const-group' => [
                <<<'CODE'
                <?php
                use const Foo\{BAR, BAZ};
                echo BAR;
                echo BAZ;
                CODE,
                <<<'EXPECT'
                <?php
                use const Foo\BAR;
                use const Foo\BAZ;

                echo BAR;
                echo BAZ;

                EXPECT,
            ],
        ];
    }
}

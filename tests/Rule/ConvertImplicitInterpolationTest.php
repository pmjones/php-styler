<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ConvertImplicitInterpolationTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new Format(rules: [
                ConvertImplicitInterpolation::class,
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
            'simple-variable' => [
                <<<'CODE'
                <?php
                $x = "foo$bar";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "foo{$bar}";

                EXPECT,
            ],
            'variable-at-start' => [
                <<<'CODE'
                <?php
                $x = "$bar baz";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$bar} baz";

                EXPECT,
            ],
            'variable-at-end' => [
                <<<'CODE'
                <?php
                $x = "foo $bar";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "foo {$bar}";

                EXPECT,
            ],
            'variable-only' => [
                <<<'CODE'
                <?php
                $x = "$bar";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$bar}";

                EXPECT,
            ],
            'multiple-variables' => [
                <<<'CODE'
                <?php
                $x = "$a and $b";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$a} and {$b}";

                EXPECT,
            ],
            'already-explicit' => [
                <<<'CODE'
                <?php
                $x = "{$bar}";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$bar}";

                EXPECT,
            ],
            'array-access' => [
                <<<'CODE'
                <?php
                $x = "$foo[0]";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$foo[0]}";

                EXPECT,
            ],
            'property-access' => [
                <<<'CODE'
                <?php
                $x = "$foo->bar";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$foo->bar}";

                EXPECT,
            ],
            'single-quoted-unchanged' => [
                <<<'CODE'
                <?php
                $x = 'foo$bar';
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'foo$bar';

                EXPECT,
            ],
            'heredoc' => [
                <<<'CODE'
                <?php
                $x = <<<EOT
                hello $bar world
                EOT;
                CODE,
                <<<'EXPECT'
                <?php
                $x = <<<EOT
                hello {$bar} world
                EOT;

                EXPECT,
            ],
            'mixed-explicit-implicit' => [
                <<<'CODE'
                <?php
                $x = "{$a} and $b";
                CODE,
                <<<'EXPECT'
                <?php
                $x = "{$a} and {$b}";

                EXPECT,
            ],
        ];
    }
}

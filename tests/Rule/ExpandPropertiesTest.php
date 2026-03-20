<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ExpandPropertiesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandProperties::class,
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
            'expand-typed-properties' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int $a;
                    public int $b;
                }

                EXPECT,
            ],
            'expand-untyped-properties' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $a;
                    public $b;
                }

                EXPECT,
            ],
            'expand-with-default-values' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a = 1, $b = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int $a = 1;
                    public int $b = 2;
                }

                EXPECT,
            ],
            'expand-with-static' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public static int $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public static int $a;
                    public static int $b;
                }

                EXPECT,
            ],
            'expand-with-readonly' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    protected readonly string $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    protected readonly string $a;
                    protected readonly string $b;
                }

                EXPECT,
            ],
            'single-property-unchanged' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int $a;
                }

                EXPECT,
            ],
            'three-properties' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public int $a, $b, $c;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int $a;
                    public int $b;
                    public int $c;
                }

                EXPECT,
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class NormalizeMemberSpacingTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                NormalizeMemberSpacing::class,
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
            'constants-no-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    const B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;
                }

                EXPECT,
            ],
            'properties-no-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $x;
                    public $y;
                }

                EXPECT,
            ],
            'methods-keep-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                    public function baz() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
            'different-types-blank-line' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    public $x;
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;

                    public $x;

                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'mixed-members-full' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1;
                    const B = 2;
                    public $x;
                    public $y;
                    public function bar() {}
                    public function baz() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;

                    public $x;
                    public $y;

                    public function bar()
                    {
                    }

                    public function baz()
                    {
                    }
                }

                EXPECT,
            ],
            'enum-cases-no-blank-line' => [
                <<<'CODE'
                <?php
                enum Color
                {
                    case Red;
                    case Green;
                    case Blue;
                }
                CODE,
                <<<'EXPECT'
                <?php
                enum Color
                {
                    case Red;
                    case Green;
                    case Blue;
                }

                EXPECT,
            ],
        ];
    }
}

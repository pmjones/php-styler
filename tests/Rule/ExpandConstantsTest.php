<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ExpandConstantsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandConstants::class,
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
            'expand-multiple-constants' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1, B = 2;
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
            'expand-with-visibility' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public const A = 1, B = 2;
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
            'expand-with-final' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    final const A = 1, B = 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    final public const A = 1;
                    final public const B = 2;
                }

                EXPECT,
            ],
            'single-constant-unchanged' => [
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
            'namespace-level-multi-constant' => [
                <<<'CODE'
                <?php
                const A = 1, B = 2;
                CODE,
                <<<'EXPECT'
                <?php
                const A = 1;
                const B = 2;

                EXPECT,
            ],
            'values-containing-arrays' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = [1, 2], B = [3, 4];
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = [1, 2];
                    public const B = [3, 4];
                }

                EXPECT,
            ],
            'three-constants' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    const A = 1, B = 2, C = 3;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public const A = 1;
                    public const B = 2;
                    public const C = 3;
                }

                EXPECT,
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExpandPropertyDeclarationsTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic' => [
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
            'untyped' => [
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
            'with-defaults' => [
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
            'union-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public string|int $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public int|string $a;
                    public int|string $b;
                }

                EXPECT,
            ],
            'nullable-type' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public ?int $a, $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public ?int $a;
                    public ?int $b;
                }

                EXPECT,
            ],
            'single-property' => [
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
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandPropertyDeclarations::class,
                InsertPublicVisibility::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

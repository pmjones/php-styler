<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeModifierOrderTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                NormalizeModifierOrder::class,
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
            'already-correct' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public static function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'reversed' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    static public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public static function bar()
                    {
                    }
                }

                EXPECT,
            ],
            'single-modifier' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() {}
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }
                }

                EXPECT,
            ],
        ];
    }
}

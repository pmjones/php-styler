<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ConvertLongTypeHintsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertLongTypeHints::class,
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
            'integer-to-int' => [
                <<<'CODE'
                <?php
                function foo(integer $x) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(int $x)
                {
                }

                EXPECT,
            ],
            'boolean-to-bool' => [
                <<<'CODE'
                <?php
                function foo(boolean $x) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(bool $x)
                {
                }

                EXPECT,
            ],
            'double-to-float-return' => [
                <<<'CODE'
                <?php
                function foo(): double {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : float
                {
                }

                EXPECT,
            ],
            'real-to-float-return' => [
                <<<'CODE'
                <?php
                function foo(): real {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo() : float
                {
                }

                EXPECT,
            ],
            'already-short' => [
                <<<'CODE'
                <?php
                function foo(int $x): bool {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(int $x) : bool
                {
                }

                EXPECT,
            ],
        ];
    }
}

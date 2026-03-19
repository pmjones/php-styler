<?php
declare(strict_types=1);

namespace PhpStyler\Format\Vendor;

use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class DoctrineFormatTest extends TestCase
{
    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(new DoctrineFormat());
    }

    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $actual = ($this->styler)($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'space-after-not' => [
                <<<'CODE'
                <?php
                $x = !$y;
                CODE,
                <<<'EXPECT'
                <?php
                $x = ! $y;

                EXPECT,
            ],
            'space-after-cast' => [
                <<<'CODE'
                <?php
                $x = (int)$y;
                CODE,
                <<<'EXPECT'
                <?php
                $x = (int) $y;

                EXPECT,
            ],
            'no-space-around-increment' => [
                <<<'CODE'
                <?php
                $i++;
                CODE,
                <<<'EXPECT'
                <?php
                $i++;

                EXPECT,
            ],
            'single-quotes' => [
                <<<'CODE'
                <?php
                $x = "hello";
                CODE,
                <<<'EXPECT'
                <?php
                $x = 'hello';

                EXPECT,
            ],
            'instantiation-parens' => [
                <<<'CODE'
                <?php
                $x = new Foo;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'concatenation-spacing' => [
                <<<'CODE'
                <?php
                $x = $a.$b;
                CODE,
                <<<'EXPECT'
                <?php
                $x = $a . $b;

                EXPECT,
            ],
            'null-last-in-return-type' => [
                <<<'CODE'
                <?php
                function foo(): null|int {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): int|null
                {
                }

                EXPECT,
            ],
            'null-last-not-nullable-shorthand' => [
                <<<'CODE'
                <?php
                function foo(): null|string {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(): string|null
                {
                }

                EXPECT,
            ],
        ];
    }
}

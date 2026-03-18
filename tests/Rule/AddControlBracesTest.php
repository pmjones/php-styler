<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\ExtendedFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class AddControlBracesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new ExtendedFormat(rules: [
                AddControlBraces::class,
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
            'basic-if' => [
                <<<'CODE'
                <?php
                if ($x)
                    $y = 1;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = 1;
                }

                EXPECT,
            ],
            'basic-else' => [
                <<<'CODE'
                <?php
                if ($x)
                    $y = 1;
                else
                    $y = 2;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = 1;
                } else {
                    $y = 2;
                }

                EXPECT,
            ],
            'basic-elseif' => [
                <<<'CODE'
                <?php
                if ($x)
                    $y = 1;
                elseif ($z)
                    $y = 2;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = 1;
                } elseif ($z) {
                    $y = 2;
                }

                EXPECT,
            ],
            'if-elseif-else-chain' => [
                <<<'CODE'
                <?php
                if ($a)
                    $x = 1;
                elseif ($b)
                    $x = 2;
                else
                    $x = 3;
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    $x = 1;
                } elseif ($b) {
                    $x = 2;
                } else {
                    $x = 3;
                }

                EXPECT,
            ],
            'basic-for' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 10; $i++)
                    $x++;
                CODE,
                <<<'EXPECT'
                <?php
                for ($i = 0; $i < 10; $i ++) {
                    $x ++;
                }

                EXPECT,
            ],
            'basic-foreach' => [
                <<<'CODE'
                <?php
                foreach ($arr as $v)
                    $x = $v;
                CODE,
                <<<'EXPECT'
                <?php
                foreach ($arr as $v) {
                    $x = $v;
                }

                EXPECT,
            ],
            'foreach-echo' => [
                <<<'CODE'
                <?php
                foreach ($arr as $v)
                    echo $v;
                CODE,
                <<<'EXPECT'
                <?php
                foreach ($arr as $v) {
                    echo $v;
                }

                EXPECT,
            ],
            'if-ternary' => [
                <<<'CODE'
                <?php
                if ($x)
                    $y = $a ? $b : $c;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = $a ? $b : $c;
                }

                EXPECT,
            ],
            'if-elvis' => [
                <<<'CODE'
                <?php
                if ($x)
                    $y = $a ?: $b;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = $a ?: $b;
                }

                EXPECT,
            ],
            'if-arrow-fn' => [
                <<<'CODE'
                <?php
                if ($x)
                    $f = fn() => $x;
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $f = fn () => $x;
                }

                EXPECT,
            ],
            'basic-while' => [
                <<<'CODE'
                <?php
                while ($x)
                    $x--;
                CODE,
                <<<'EXPECT'
                <?php
                while ($x) {
                    $x --;
                }

                EXPECT,
            ],
            'already-braced' => [
                <<<'CODE'
                <?php
                if ($x) {
                    $y = 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($x) {
                    $y = 1;
                }

                EXPECT,
            ],
        ];
    }
}

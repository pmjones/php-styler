<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\ExtendedFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class AddBlankLineBeforeReturnTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new ExtendedFormat(rules: [
                AddBlankLineBeforeReturn::class,
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
            'basic-return' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $x = 1;
                    return $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $x = 1;

                    return $x;
                }

                EXPECT,
            ],
            'first-statement-in-function' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    return 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return 1;
                }

                EXPECT,
            ],
            'already-has-blank-line' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $x = 1;

                    return $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $x = 1;

                    return $x;
                }

                EXPECT,
            ],
            'multiple-statements' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    $a = 1;
                    $b = 2;
                    return $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    $a = 1;
                    $b = 2;

                    return $b;
                }

                EXPECT,
            ],
            'return-after-if-block' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                    return $x;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }

                    return $x;
                }

                EXPECT,
            ],
        ];
    }
}

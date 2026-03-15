<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class NormalizeTrailingCommasTest extends TestCase
{
    private function assertStyled(
        string $code,
        string $expect,
        int $lineLen = 44,
    ) : void
    {
        $styler = new Styler(
            lineLen: $lineLen,
            eol: "\n",
            rules: [new NormalizeTrailingCommas()],
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect, int $lineLen = 44) : void
    {
        $this->assertStyled($code, $expect, $lineLen);
    }

    /** @return array<string, array{0: string, 1: string, 2?: int}> */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'trailing-comma-removed-single-line' => [
                <<<'CODE'
                <?php
                foo($a, $b,);
                CODE,
                <<<'EXPECT'
                <?php
                foo($a, $b);

                EXPECT,
            ],

            'trailing-comma-added-multi-line' => [
                <<<'CODE'
                <?php
                someFunction($veryLongArgumentOne, $veryLongArgumentTwo, $veryLongArgumentThree);
                CODE,
                <<<'EXPECT'
                <?php
                someFunction(
                    $veryLongArgumentOne,
                    $veryLongArgumentTwo,
                    $veryLongArgumentThree,
                );

                EXPECT,
            ],

            'trailing-comma-preserved-multi-line' => [
                <<<'CODE'
                <?php
                someFunc($longArgAlpha, $longArgBravo, $longArgCharlie,);
                CODE,
                <<<'EXPECT'
                <?php
                someFunc(
                    $longArgAlpha,
                    $longArgBravo,
                    $longArgCharlie,
                );

                EXPECT,
            ],

            'trailing-comma-with-comment' => [
                <<<'CODE'
                <?php
                function foo($longParamAlpha, $longParamBravo, $longParamCharlie) // comment
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo(
                    $longParamAlpha,
                    $longParamBravo,
                    $longParamCharlie,
                ) // comment
                {
                }

                EXPECT,
            ],

            'array-trailing-comma-added' => [
                <<<'CODE'
                <?php
                $arr = [$veryLongValueOne, $veryLongValueTwo, $veryLongValueThree];
                CODE,
                <<<'EXPECT'
                <?php
                $arr = [
                    $veryLongValueOne,
                    $veryLongValueTwo,
                    $veryLongValueThree,
                ];

                EXPECT,
            ],

            'params-trailing-comma-added' => [
                <<<'CODE'
                <?php
                function foo($longParamAlpha, $longParamBravo, $longParamCharlie) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(
                    $longParamAlpha,
                    $longParamBravo,
                    $longParamCharlie,
                ) {
                }

                EXPECT,
            ],

            'empty-construct-no-comma' => [
                <<<'CODE'
                <?php
                foo();
                CODE,
                <<<'EXPECT'
                <?php
                foo();

                EXPECT,
            ],

        ];
    }
}

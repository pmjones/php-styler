<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RejoinOrphansTest extends TestCase
{
    private function assertStyled(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                lineLen: 44,
                rules: [
                    RejoinOrphans::class,
                    RemoveTrailingBlankLines::class,
                ],
            ),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $this->assertStyled($code, $expect);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'orphan-closing-paren-before-brace' => [
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

            'no-rejoin-when-multi-token-line' => [
                <<<'CODE'
                <?php
                function foo(string $longAlpha, int $longBravo, bool $longCharlie) : void {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(
                    string $longAlpha,
                    int $longBravo,
                    bool $longCharlie,
                ) : void
                {
                }

                EXPECT,
            ],

            'no-rejoin-when-next-is-not-function-brace' => [
                <<<'CODE'
                <?php
                class Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                }

                EXPECT,
            ],
        ];
    }
}

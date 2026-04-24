<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveLanguageConstructParensTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'echo-parens' => [
                <<<'CODE'
                <?php
                echo($x);
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'return-parens' => [
                <<<'CODE'
                <?php
                function foo() {
                    return($x);
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return $x;
                }

                EXPECT,
            ],
            'include-parens' => [
                <<<'CODE'
                <?php
                include($file);
                CODE,
                <<<'EXPECT'
                <?php
                include $file;

                EXPECT,
            ],
            'already-no-parens' => [
                <<<'CODE'
                <?php
                echo $x;
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'return-no-argument' => [
                <<<'CODE'
                <?php
                function foo() {
                    return;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return;
                }

                EXPECT,
            ],
            'complex-expression' => [
                <<<'CODE'
                <?php
                echo($a + $b);
                CODE,
                <<<'EXPECT'
                <?php
                echo $a + $b;

                EXPECT,
            ],
            'print-parens' => [
                <<<'CODE'
                <?php
                print($x);
                CODE,
                <<<'EXPECT'
                <?php
                print $x;

                EXPECT,
            ],
            'require-parens' => [
                <<<'CODE'
                <?php
                require($file);
                CODE,
                <<<'EXPECT'
                <?php
                require $file;

                EXPECT,
            ],
            'require-once-parens' => [
                <<<'CODE'
                <?php
                require_once($file);
                CODE,
                <<<'EXPECT'
                <?php
                require_once $file;

                EXPECT,
            ],
            'echo-no-parens-no-change' => [
                <<<'CODE'
                <?php
                echo $x;
                CODE,
                <<<'EXPECT'
                <?php
                echo $x;

                EXPECT,
            ],
            'echo-parens-followed-by-concat-not-removed' => [
                <<<'CODE'
                <?php
                echo ($x) . $y;
                CODE,
                <<<'EXPECT'
                <?php
                echo ($x) . $y;

                EXPECT,
            ],
            'return-parens-followed-by-arithmetic-not-removed' => [
                <<<'CODE'
                <?php
                function foo() {
                    return ($a) + $b;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return ($a) + $b;
                }

                EXPECT,
            ],
            'print-parens-removed-before-semicolon' => [
                <<<'CODE'
                <?php
                $x = print($y);
                CODE,
                <<<'EXPECT'
                <?php
                $x = print $y;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveLanguageConstructParens::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

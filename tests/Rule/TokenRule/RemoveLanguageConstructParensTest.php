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

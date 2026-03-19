<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RemoveParensFromLanguageConstructsTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveParensFromLanguageConstructs::class,
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
        ];
    }
}

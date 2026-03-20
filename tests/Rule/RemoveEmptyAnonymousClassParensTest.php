<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RemoveEmptyAnonymousClassParensTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveEmptyAnonymousClassParens::class,
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
            'remove-empty-parens' => [
                <<<'CODE'
                <?php
                $x = new class() {
                    public function foo() {}
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                    public function foo()
                    {
                    }
                };

                EXPECT,
            ],
            'keep-parens-with-args' => [
                <<<'CODE'
                <?php
                $x = new class($y) {
                    public function foo() {}
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class ($y) {
                    public function foo()
                    {
                    }
                };

                EXPECT,
            ],
            'no-parens-unchanged' => [
                <<<'CODE'
                <?php
                $x = new class {
                    public function foo() {}
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                    public function foo()
                    {
                    }
                };

                EXPECT,
            ],
        ];
    }
}

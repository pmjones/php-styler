<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveEmptyAnonymousClassParensTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'empty-parens' => [
                <<<'CODE'
                <?php
                $x = new class () {
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                };

                EXPECT,
            ],
            'non-empty-parens-kept' => [
                <<<'CODE'
                <?php
                $x = new class ($arg) {
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class ($arg) {
                };

                EXPECT,
            ],
            'no-parens' => [
                <<<'CODE'
                <?php
                $x = new class {
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = new class {
                };

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
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
}

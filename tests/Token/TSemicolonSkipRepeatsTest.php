<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TSemicolonSkipRepeatsTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                parseAs: [TSemicolon::class => TSemicolonSkipRepeats::class],
                rules: [RemoveTrailingBlankLines::class],
            ),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'double-semicolon' => [
                <<<'CODE'
                <?php
                $x = 1;;
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
            'single-semicolon' => [
                <<<'CODE'
                <?php
                $x = 1;
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
            'for-loop-unchanged' => [
                <<<'CODE'
                <?php
                for (;;) {
                }
                CODE,
                <<<'EXPECT'
                <?php
                for (; ; ) {
                }

                EXPECT,
            ],
        ];
    }
}

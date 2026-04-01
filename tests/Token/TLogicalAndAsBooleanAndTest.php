<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TLogicalAndAsBooleanAndTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                parseAs: [TLogicalAnd::class => TLogicalAndAsBooleanAnd::class],
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
            'and-to-boolean-and' => [
                <<<'CODE'
                <?php
                $a = $b and $c;
                CODE,
                <<<'EXPECT'
                <?php
                $a = $b && $c;

                EXPECT,
            ],
            'already-boolean-and' => [
                <<<'CODE'
                <?php
                $a = $b && $c;
                CODE,
                <<<'EXPECT'
                <?php
                $a = $b && $c;

                EXPECT,
            ],
            'xor-unchanged' => [
                <<<'CODE'
                <?php
                $a = $b xor $c;
                CODE,
                <<<'EXPECT'
                <?php
                $a = $b xor $c;

                EXPECT,
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertToShortArraySyntaxTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'array-construct' => [
                <<<'CODE'
                <?php
                $x = array(1, 2);
                CODE,
                <<<'EXPECT'
                <?php
                $x = [1, 2];

                EXPECT,
            ],
            'empty-array' => [
                <<<'CODE'
                <?php
                $x = array();
                CODE,
                <<<'EXPECT'
                <?php
                $x = [];

                EXPECT,
            ],
            'array-typehint-unchanged' => [
                <<<'CODE'
                <?php
                function foo(array $x) {}
                CODE,
                <<<'EXPECT'
                <?php
                function foo(array $x)
                {
                }

                EXPECT,
            ],
            'nested-array' => [
                <<<'CODE'
                <?php
                $x = array(1, array(2, 3));
                CODE,
                <<<'EXPECT'
                <?php
                $x = [1, [2, 3]];

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertToShortArraySyntax::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

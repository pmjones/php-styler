<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertToShortListSyntaxTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'list-construct' => [
                <<<'CODE'
                <?php
                list($a, $b) = $x;
                CODE,
                <<<'EXPECT'
                <?php
                [$a, $b] = $x;

                EXPECT,
            ],
            'nested-list' => [
                <<<'CODE'
                <?php
                list($a, list($b, $c)) = $x;
                CODE,
                <<<'EXPECT'
                <?php
                [$a, [$b, $c]] = $x;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertToShortListSyntax::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

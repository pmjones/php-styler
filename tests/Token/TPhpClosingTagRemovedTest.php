<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class TPhpClosingTagRemovedTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                parseAs: [TPhpClosingTag::class => TPhpClosingTagRemoved::class],
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
            'remove-closing-tag' => [
                <<<'CODE'
                <?php
                $x = 1;
                ?>
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
            'no-closing-tag' => [
                <<<'CODE'
                <?php
                $x = 1;
                CODE,
                <<<'EXPECT'
                <?php
                $x = 1;

                EXPECT,
            ],
        ];
    }
}

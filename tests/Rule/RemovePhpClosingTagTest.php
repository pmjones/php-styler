<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RemovePhpClosingTagTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemovePhpClosingTag::class,
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

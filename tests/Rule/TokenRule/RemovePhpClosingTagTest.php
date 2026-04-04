<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemovePhpClosingTagTest extends TestCase
{
    #[DataProvider('provide')]
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
            'trailing-tag-removed' => [
                '<?php $x = 1; ?>',
                <<<'EXPECT'
                <?php $x = 1;

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

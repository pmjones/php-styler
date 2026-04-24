<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveLeadingWhitespaceTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'leading-spaces' => [
                "   <?php\necho 1;\n",
                "<?php\n echo 1;\n",
            ],
            'leading-newlines' => [
                "\n\n<?php\necho 2;\n",
                "<?php\n echo 2;\n",
            ],
            'leading-mixed-whitespace' => [
                " \t\n \t <?php\necho 3;\n",
                "<?php\n echo 3;\n",
            ],
            'leading-non-whitespace-html-preserved' => [
                "html<?php\necho 4;\n",
                "html<?php\n echo 4;\n",
            ],
            'no-leading-content' => [
                "<?php\necho 5;\n",
                "<?php\necho 5;\n",
            ],
            'empty-input' => [
                '',
                "\n",
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveLeadingWhitespace::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveBomTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        $bom = "\xEF\xBB\xBF";

        return [
            'bom-only-inline-html' => [
                $bom . "<?php\necho 1;\n",
                "<?php\n echo 1;\n",
            ],
            'bom-with-trailing-html' => [
                $bom . "html<?php\necho 2;\n",
                "html<?php\n echo 2;\n",
            ],
            'no-bom-unchanged' => ["<?php\necho 3;\n", "<?php\necho 3;\n"],
            'empty-input' => ['', "\n"],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveBom::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertVarToPublicTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    var $a;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $a;
                }

                EXPECT,
            ],
            'non-var-unchanged' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public $a;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public $a;
                }

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ConvertVarToPublic::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

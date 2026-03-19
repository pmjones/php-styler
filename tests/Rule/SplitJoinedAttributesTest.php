<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class SplitJoinedAttributesTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                SplitJoinedAttributes::class,
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
            'split-two-attributes' => [
                <<<'CODE'
                <?php
                #[Foo, Bar]
                class Baz {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                #[Bar]
                class Baz
                {
                }

                EXPECT,
            ],
            'split-three-attributes' => [
                <<<'CODE'
                <?php
                #[Foo, Bar, Baz]
                function qux() {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                #[Bar]
                #[Baz]
                function qux()
                {
                }

                EXPECT,
            ],
            'split-with-args' => [
                <<<'CODE'
                <?php
                #[Foo(1, 2), Bar]
                class Baz {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo(1, 2)]
                #[Bar]
                class Baz
                {
                }

                EXPECT,
            ],
            'single-attribute-unchanged' => [
                <<<'CODE'
                <?php
                #[Foo]
                class Bar {}
                CODE,
                <<<'EXPECT'
                <?php
                #[Foo]
                class Bar
                {
                }

                EXPECT,
            ],
        ];
    }
}

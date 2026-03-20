<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ExpandTraitUseTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                ExpandTraitUse::class,
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
            'expand-multiple-traits' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar, Baz;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use Bar;
                    use Baz;
                }

                EXPECT,
            ],
            'single-trait-unchanged' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use Bar;
                }

                EXPECT,
            ],
            'three-traits' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar, Baz, Qux;
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    use Bar;
                    use Baz;
                    use Qux;
                }

                EXPECT,
            ],
        ];
    }
}

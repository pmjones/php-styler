<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CollapseEmptyBodyTest extends TestCase
{
    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                CollapseEmptyBody::class,
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
            'empty-function' => [
                <<<'CODE'
                <?php
                function foo()
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo() {}

                EXPECT,
            ],
            'non-empty-function' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    return 1;
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    return 1;
                }

                EXPECT,
            ],
            'empty-class' => [
                <<<'CODE'
                <?php
                class Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo {}

                EXPECT,
            ],
            'empty-interface' => [
                <<<'CODE'
                <?php
                interface Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                interface Foo {}

                EXPECT,
            ],
            'empty-trait' => [
                <<<'CODE'
                <?php
                trait Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                trait Foo {}

                EXPECT,
            ],
            'empty-enum' => [
                <<<'CODE'
                <?php
                enum Foo
                {
                }
                CODE,
                <<<'EXPECT'
                <?php
                enum Foo {}

                EXPECT,
            ],
            'empty-method-in-class' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar()
                    {
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                class Foo
                {
                    public function bar() {}
                }

                EXPECT,
            ],
            'empty-closure' => [
                <<<'CODE'
                <?php
                $x = function () {
                };
                CODE,
                <<<'EXPECT'
                <?php
                $x = function () {};

                EXPECT,
            ],
        ];
    }
}

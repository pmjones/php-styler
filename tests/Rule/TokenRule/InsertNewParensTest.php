<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\LineRule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InsertNewParensTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'bare-unqualified' => [
                <<<'CODE'
                <?php
                $x = new Foo;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'bare-qualified' => [
                <<<'CODE'
                <?php
                $x = new Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo\Bar();

                EXPECT,
            ],
            'bare-fully-qualified' => [
                <<<'CODE'
                <?php
                $x = new \Foo\Bar;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new \Foo\Bar();

                EXPECT,
            ],
            'bare-variable' => [
                <<<'CODE'
                <?php
                $x = new $class;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new $class();

                EXPECT,
            ],
            'already-has-parens' => [
                <<<'CODE'
                <?php
                $x = new Foo();
                CODE,
                <<<'EXPECT'
                <?php
                $x = new Foo();

                EXPECT,
            ],
            'variable-with-property-access-skipped' => [
                <<<'CODE'
                <?php
                $x = new $class->prop;
                CODE,
                <<<'EXPECT'
                <?php
                $x = new $class->prop;

                EXPECT,
            ],
        ];
    }

    #[DataProvider('provide')]
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                InsertNewParens::class,
                RemoveTrailingBlankLines::class,
            ]),
        );

        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }
}

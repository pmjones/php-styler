<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class RemoveImportLeadingBackslashTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                RemoveImportLeadingBackslash::class,
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
            'leading-backslash-removed' => [
                <<<'CODE'
                <?php
                use \Foo\Bar;
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                new Bar();

                EXPECT,
            ],
            'no-leading-backslash-unchanged' => [
                <<<'CODE'
                <?php
                use Foo\Bar;
                new Bar();
                CODE,
                <<<'EXPECT'
                <?php
                use Foo\Bar;

                new Bar();

                EXPECT,
            ],
            'use-function-leading-backslash' => [
                <<<'CODE'
                <?php
                use function \Foo\bar;
                bar();
                CODE,
                <<<'EXPECT'
                <?php
                use function Foo\bar;

                bar();

                EXPECT,
            ],
            'use-const-leading-backslash' => [
                <<<'CODE'
                <?php
                use const \Foo\BAR;
                echo BAR;
                CODE,
                <<<'EXPECT'
                <?php
                use const Foo\BAR;

                echo BAR;

                EXPECT,
            ],
            'fqn-outside-import-unchanged' => [
                <<<'CODE'
                <?php
                $x = new \Foo\Bar();
                CODE,
                <<<'EXPECT'
                <?php
                $x = new \Foo\Bar();

                EXPECT,
            ],
        ];
    }
}

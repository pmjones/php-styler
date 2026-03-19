<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class AddBlankLineAfterBlockTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(rules: [
                AddBlankLineAfterBlock::class,
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
            'blank-line-after-if' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }

                    baz();
                }

                EXPECT,
            ],
            'no-double-blank-line' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }

                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }

                    baz();
                }

                EXPECT,
            ],
            'no-blank-line-between-if-else' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    } else {
                        baz();
                    }
                    qux();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    } else {
                        baz();
                    }

                    qux();
                }

                EXPECT,
            ],
            'no-blank-line-between-try-catch-finally' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    try {
                        bar();
                    } catch (Exception $e) {
                        baz();
                    } finally {
                        qux();
                    }
                    done();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    try {
                        bar();
                    } catch (Exception $e) {
                        baz();
                    } finally {
                        qux();
                    }

                    done();
                }

                EXPECT,
            ],
            'no-blank-line-before-closing-brace' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    if ($x) {
                        bar();
                    }
                }

                EXPECT,
            ],
            'blank-line-after-do-while' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    do {
                        bar();
                    } while ($x);
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    do {
                        bar();
                    } while ($x);

                    baz();
                }

                EXPECT,
            ],
            'blank-line-after-foreach' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    foreach ($items as $item) {
                        bar($item);
                    }
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    foreach ($items as $item) {
                        bar($item);
                    }

                    baz();
                }

                EXPECT,
            ],
            'blank-line-after-switch' => [
                <<<'CODE'
                <?php
                function foo()
                {
                    switch ($x) {
                        case 1:
                            break;
                    }
                    baz();
                }
                CODE,
                <<<'EXPECT'
                <?php
                function foo()
                {
                    switch ($x) {
                        case 1:
                            break;
                    }

                    baz();
                }

                EXPECT,
            ],
        ];
    }
}

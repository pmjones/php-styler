<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Rule\RemoveTrailingBlankLines;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class TElseAsElseIfTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new DeclarationFormat(
                parseAs: [TElse::class => TElseAsElseIf::class],
                rules: [RemoveTrailingBlankLines::class],
            ),
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic-else-if' => [
                <<<'CODE'
                <?php
                if ($a) {
                    foo();
                } else if ($b) {
                    bar();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                }

                EXPECT,
            ],
            'already-elseif-unchanged' => [
                <<<'CODE'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                }

                EXPECT,
            ],
            'nested-else-if' => [
                <<<'CODE'
                <?php
                if ($a) {
                    foo();
                } else if ($b) {
                    bar();
                } else if ($c) {
                    baz();
                } else {
                    qux();
                }
                CODE,
                <<<'EXPECT'
                <?php
                if ($a) {
                    foo();
                } elseif ($b) {
                    bar();
                } elseif ($c) {
                    baz();
                } else {
                    qux();
                }

                EXPECT,
            ],
        ];
    }
}

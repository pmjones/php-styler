<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class ConvertElseIfTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(eol: "\n", rules: [new ConvertElseIf()]);
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

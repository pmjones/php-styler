<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Format\ExtendedFormat;
use PhpStyler\Styler;
use PHPUnit\Framework\TestCase;

class ConvertSwitchContinueToBreakTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(
            new ExtendedFormat(rules: [
                ConvertSwitchContinueToBreak::class,
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
            'basic-continue-in-switch' => [
                <<<'CODE'
                <?php
                switch ($x) {
                    case 1:
                        continue;
                }
                CODE,
                <<<'EXPECT'
                <?php
                switch ($x) {
                    case 1:
                        break;
                }

                EXPECT,
            ],
            'continue-in-loop-inside-switch' => [
                <<<'CODE'
                <?php
                switch ($x) {
                    case 1:
                        for ($i = 0; $i < 10; $i++) {
                            continue;
                        }
                }
                CODE,
                <<<'EXPECT'
                <?php
                switch ($x) {
                    case 1:
                        for ($i = 0; $i < 10; $i ++) {
                            continue;
                        }
                }

                EXPECT,
            ],
            'break-already' => [
                <<<'CODE'
                <?php
                switch ($x) {
                    case 1:
                        break;
                }
                CODE,
                <<<'EXPECT'
                <?php
                switch ($x) {
                    case 1:
                        break;
                }

                EXPECT,
            ],
            'continue-in-standalone-loop' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 10; $i++) {
                    continue;
                }
                CODE,
                <<<'EXPECT'
                <?php
                for ($i = 0; $i < 10; $i ++) {
                    continue;
                }

                EXPECT,
            ],
            'continue-2-skip' => [
                <<<'CODE'
                <?php
                switch ($x) {
                    case 1:
                        continue 2;
                }
                CODE,
                <<<'EXPECT'
                <?php
                switch ($x) {
                    case 1:
                        continue 2;
                }

                EXPECT,
            ],
        ];
    }
}

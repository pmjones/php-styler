<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class ConvertListToArrayTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(eol: "\n", rules: [new ConvertListToArray()]);
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic-list' => [
                <<<'CODE'
                <?php list($a, $b) = $arr;
                CODE,
                <<<'EXPECT'
                <?php [$a, $b] = $arr;

                EXPECT,
            ],
            'list-with-keys' => [
                <<<'CODE'
                <?php list('a' => $a, 'b' => $b) = $arr;
                CODE,
                <<<'EXPECT'
                <?php ['a' => $a, 'b' => $b] = $arr;

                EXPECT,
            ],
            'already-short-unchanged' => [
                <<<'CODE'
                <?php [$a, $b] = $arr;
                CODE,
                <<<'EXPECT'
                <?php [$a, $b] = $arr;

                EXPECT,
            ],
        ];
    }
}

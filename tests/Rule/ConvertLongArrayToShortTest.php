<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class ConvertLongArrayToShortTest extends TestCase
{
    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $styler = new Styler(eol: "\n", rules: [new ConvertLongArrayToShort()]);
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        return [
            'basic-array' => [
                <<<'CODE'
                <?php $a = array(1, 2, 3);
                CODE,
                <<<'EXPECT'
                <?php $a = [1, 2, 3];

                EXPECT,
            ],
            'empty-array' => [
                <<<'CODE'
                <?php $a = array();
                CODE,
                <<<'EXPECT'
                <?php $a = [];

                EXPECT,
            ],
            'nested-array' => [
                <<<'CODE'
                <?php $a = array(array(1, 2), array(3, 4));
                CODE,
                <<<'EXPECT'
                <?php $a = [[1, 2], [3, 4]];

                EXPECT,
            ],
            'array-as-argument' => [
                <<<'CODE'
                <?php foo(array(1, 2));
                CODE,
                <<<'EXPECT'
                <?php foo([1, 2]);

                EXPECT,
            ],
            'already-short-unchanged' => [
                <<<'CODE'
                <?php $a = [1, 2, 3];
                CODE,
                <<<'EXPECT'
                <?php $a = [1, 2, 3];

                EXPECT,
            ],
            'array-with-keys' => [
                <<<'CODE'
                <?php $a = array('a' => 1, 'b' => 2);
                CODE,
                <<<'EXPECT'
                <?php $a = ['a' => 1, 'b' => 2];

                EXPECT,
            ],
        ];
    }
}

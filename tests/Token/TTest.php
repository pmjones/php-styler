<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TTest extends \PHPUnit\Framework\TestCase
{
    public function testDebugInfo() : void
    {
        $fake = new TFake(T_OPEN_TAG, 'fake', 79, 88);

        $expect = [
            'CLASS' => TFake::class,
            'TOKEN' => 'T_OPEN_TAG',
            'id' => T_OPEN_TAG,
            'text' => 'fake',
            'line' => 79,
            'pos' => 88,
            'parenDepth' => 0,
            'argCount' => 0,
            'openingToken' => null,
            'closingToken' => null,
            'style' => null,
        ];

        $actual = $fake->__debugInfo();
        $this->assertSame($expect, $fake->__debugInfo());
    }
}

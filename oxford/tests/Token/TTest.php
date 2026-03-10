<?php
declare(strict_types=1);

namespace Oxford\Token;

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
            'rejoinOrphanBefore' => false,
            'parenDepth' => 0,
            'argCount' => 0,
            'openingToken' => null,
            'closingToken' => null,
            'transparentOpener' => false,
        ];

        $actual = $fake->__debugInfo();
        $this->assertSame($expect, $fake->__debugInfo());
    }
}

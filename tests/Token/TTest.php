<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Style;

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
            'openingToken' => null,
            'closingToken' => null,
            'style' => null,
        ];

        $this->assertSame($expect, $fake->__debugInfo());
    }

    public function testNew() : void
    {
        $source = new \PhpToken(T_STRING, 'Hello', 1, 0);
        $style = new Style();

        $token = AToken::new($source, TFake::class, $style);

        $this->assertInstanceOf(TFake::class, $token);
        $this->assertSame('Hello', $token->text);
        $this->assertSame($style, $token->style);
    }

    public function testNewAppliesCase() : void
    {
        $source = new \PhpToken(T_STRING, 'Hello', 1, 0);
        $style = new Style(case: 'strtolower');

        $token = AToken::new($source, TFake::class, $style);

        $this->assertSame('hello', $token->text);
    }

    public function testPair() : void
    {
        $opener = new TFake(T_STRING, '(', 1, 0);
        $closer = new TFake(T_STRING, ')', 1, 1);

        AToken::pair($opener, $closer);

        $this->assertSame($closer, $opener->closingToken);
        $this->assertSame($opener, $closer->openingToken);
    }
}

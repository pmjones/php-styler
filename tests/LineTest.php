<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TSpace;
use PHPUnit\Framework\TestCase;

class LineTest extends TestCase
{
    public function testFirstContentTokenReturnsNullForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertNull($line->firstContentToken());
    }

    public function testLastContentTokenReturnsNullForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertNull($line->lastContentToken());
    }

    public function testLastContentIndexReturnsZeroForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertSame(0, $line->lastContentIndex());
    }
}

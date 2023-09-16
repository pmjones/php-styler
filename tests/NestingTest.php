<?php
declare(strict_types=1);

namespace PhpStyler;

class NestingTest extends TestCase
{
    public function testDecr() : void
    {
        $nesting = new Nesting();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot decrease fake nesting level below zero');
        $nesting->decr('fake');
    }
}

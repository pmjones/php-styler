<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;

class NestingTest extends TestCase
{
    public function testDecr() : void
    {
        $nesting = new Nesting();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot decrease fake nesting level below zero');
        $nesting->decr('fake');
    }
}

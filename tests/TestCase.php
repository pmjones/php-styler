<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Service $service;

    protected Styler $styler;

    protected function setUp() : void
    {
        $this->service = new Service(new Styler());
    }

    protected function print(string $source) : string
    {
        return $this->service->__invoke($source);
    }

    protected function assertPrint(string $expect, string $source) : void
    {
        $actual = $this->print($source);
        $this->assertSame($expect, $actual);
    }
}

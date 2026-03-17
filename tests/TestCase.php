<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format;
use PhpStyler\Rule\RemoveTrailingBlankLines;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(new Format(rules: [new RemoveTrailingBlankLines()]));
    }

    protected function print(string $source) : string
    {
        return $this->styler->__invoke($source);
    }

    protected function assertPrint(string $expect, string $source) : void
    {
        $actual = $this->print($source);
        $actual = str_replace("\r\n", "\n", $actual);
        $expect = str_replace("\r\n", "\n", $expect);
        $this->assertSame($expect, $actual);
    }
}

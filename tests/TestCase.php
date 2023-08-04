<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\ParserFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Printer $printer;

    protected Styler $styler;

    protected function setUp() : void
    {
        $this->printer = new Printer();
        $this->styler = new Styler();
    }

    protected function print(string $source) : string
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        /** @var array<\PhpParser\Node\Stmt> */
        $stmts = $parser->parse($source);
        return $this->printer->printFile($stmts, $this->styler);
    }

    protected function assertPrint(string $expect, string $source) : void
    {
        $actual = $this->print($source);
        $this->assertSame($expect, $actual);
    }
}

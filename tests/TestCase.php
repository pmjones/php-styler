<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

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
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);

        /** @var array<\PhpParser\Node\Stmt> */
        $stmts = $parser->parse($source);

        // additional information for styler
        $visitor = new Visitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($stmts);

        // printable form
        $printables = $this->printer->__invoke($stmts);
        return $this->styler->__invoke($printables);
    }

    protected function assertPrint(string $expect, string $source) : void
    {
        $actual = $this->print($source);
        $this->assertSame($expect, $actual);
    }
}

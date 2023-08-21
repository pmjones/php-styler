<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class Service
{
    protected NodeTraverser $nodeTraverser;

    protected Parser $parser;

    protected Printer $printer;

    public function __construct(
        protected Styler $styler,
        protected bool $debugParser = false,
        protected bool $debugPrinter = false,
    ) {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $this->printer = new Printer();
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeTraverser->addVisitor(new Visitor());
    }

    public function __invoke(string $code) : string
    {
        $debug = '';

        /** @var array<\PhpParser\Node\Stmt> */
        $stmts = $this->parser->parse($code);
        $this->nodeTraverser->traverse($stmts);

        if ($this->debugParser) {
            $debug .= $this->dump("Parser nodes: ", $stmts);
        }

        $printables = $this->printer->__invoke($stmts);

        if ($this->debugPrinter) {
            $debug .= $this->dump("Printables: ", $printables);
        }

        return $debug . $this->styler->__invoke($printables);
    }

    protected function dump(string $label, mixed $value) : string
    {
        ob_start();
        var_dump($value);
        return $label . ob_get_clean();
    }
}

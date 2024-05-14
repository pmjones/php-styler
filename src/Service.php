<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\Lexer;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpToken;

class Service
{
    protected NodeTraverser $nodeTraverser;

    protected Parser $parser;

    protected Printer $printer;

    public function __construct(
        protected Styler $styler,
        protected bool $debugParser = false,
        protected bool $debugPrinter = false,
        protected bool $debugStyler = false,
    ) {
        $this->parser = new Parser(new Lexer\Emulative());
        $this->printer = new Printer();
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeTraverser->addVisitor(new Visitor());
    }

    public function __invoke(string $code) : string
    {
        $debug = '';

        /** @var Stmt[] */
        $stmts = $this->parser->parse($code);
        $this->nodeTraverser->traverse($stmts);

        if ($this->debugParser) {
            $debug .= $this->dump("Parser Nodes: ", $stmts);
        }

        $printables = $this->printer->__invoke($stmts);

        if ($this->debugPrinter) {
            $debug .= $this->dump("Printables: ", $printables);
        }

        return $debug . $this->styler->__invoke($printables, $this->debugStyler);
    }

    protected function dump(string $label, mixed $value) : string
    {
        ob_start();
        var_dump($value);
        return $label . ob_get_clean();
    }
}

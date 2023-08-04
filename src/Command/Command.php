<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

abstract class Command
{
    protected Parser $parser;

    protected Printer $printer;

    protected Styler $styler;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Printer();
    }

    protected function load(string $file) : array
    {
        return require $file;
    }

    protected function lint(string $file) : bool
    {
        exec("php -l {$file}", $output, $return);

        if ($return !== 0) {
            echo implode(PHP_EOL, $output) . PHP_EOL;

            return false;
        }

        return true;
    }

    protected function setStyler(array $config) : void
    {
        $styler = $config['styler'] ?? [];

        if (is_array($styler)) {
            $this->styler = new Styler(...$styler);
        } else {
            $this->styler = $styler;
        }
    }

    protected function style(string $file) : string
    {
        $stmts = $this->parser->parse(file_get_contents($file));

        return $this->printer->printFile($stmts, $this->styler);
    }
}

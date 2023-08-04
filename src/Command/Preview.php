<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Preview
{
    protected Parser $parser;

    protected Printer $printer;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Printer();
    }

    public function __invoke(string $configFile, string $sourceFile) : int
    {
        // load config
        $config = $this->load($configFile);
        unset($config['cache']);
        unset($config['files']);

        // get the configured styler object
        $this->styler = $config['styler'] ?? null;
        unset($config['styler']);
        $this->styler ??= new Styler(...$config);

        if (! $this->lint($sourceFile)) {
            exit(1);
        }

        $stmts = $this->parser->parse(file_get_contents($sourceFile));
        echo $this->printer->printFile($stmts, $this->styler);

        return 0;
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
}

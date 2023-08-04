<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpStyler\Printer;
use PhpStyler\Styler;

class Apply
{
    protected Parser $parser;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }

    public function __invoke(string $configFile) : int
    {
        $time = time();

        // load config
        $config = $this->loadConfig($configFile);
        $files = $config['files'] ?? [];
        unset($config['files']);

        // set up services
        $this->printer = new Printer();
        $this->styler = new Styler(...$config);
        $count = 0;

        // apply to files
        foreach ($files as $file) {
            $count ++;
            $file = (string) $file;

            if (is_dir($file)) {
                echo $file . PHP_EOL;
                continue;
            }

            if (! $this->lint($file)) {
                return 1;
            }

            $this->replace($file);
        }

        echo PHP_EOL
            . "{$count} files styled in "
            . (time() - $time)
            . ' seconds.'
            . PHP_EOL
        ;
        return 0;
    }

    protected function loadConfig(string $configFile) : array
    {
        return require $configFile;
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

    protected function replace(string $file) : void
    {
        $stmts = $this->parser->parse(file_get_contents($file));
        $code = $this->printer->printFile($stmts, $this->styler);
        file_put_contents($file, $code);
        echo $file . PHP_EOL;
    }
}

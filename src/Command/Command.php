<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\Node\Stmt;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpStyler\Config;
use PhpStyler\Printer;
use PhpStyler\Styler;
use RuntimeException;
use UnexpectedValueException;

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

    protected function load(string $file) : mixed
    {
        return require $file;
    }

    protected function loadConfigFile(string $configFile) : Config
    {
        /** @var Config */
        return $this->load($configFile);
    }

    protected function findConfigFile() : string
    {
        $file = dirname(__DIR__, 5) . DIRECTORY_SEPARATOR . "php-styler.php";

        if (file_exists($file)) {
            return $file;
        }

        throw new RuntimeException("Could not find {$file}");
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

    protected function style(string $file, Styler $styler) : string
    {
        /** @var string */
        $code = file_get_contents($file);

        /** @var Stmt[] */
        $stmts = $this->parser->parse($code);
        return $this->printer->printFile($stmts, $styler);
    }
}

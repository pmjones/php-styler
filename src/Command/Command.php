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

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
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

    protected function style(string $file, Styler $styler) : string
    {
        /** @var string */
        $code = file_get_contents($file);

        /** @var Stmt[] */
        $stmts = $this->parser->parse($code);
        $printables = $this->printer->__invoke($stmts);
        return $styler->__invoke($printables);
    }
}

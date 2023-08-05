<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpParser\ParserFactory;
use PhpParser\Parser;
use PhpParser\Node\Stmt;
use PhpStyler\Printer;
use PhpStyler\Styler;
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

    /**
     * @return mixed[]
     */
    protected function load(string $file) : array
    {
        return require $file;
    }

    protected function findConfig() : string
    {
        //  6         5      4       3          2   1
        // {$PROJECT}/vendor/pmjones/php-styler/src/Command/Command.php
        $files = [dirname(__DIR__, 6)
            . DIRECTORY_SEPARATOR
            . ".php-styler.php"
        , dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . ".php-styler.php"
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }

        throw new RuntimeException("Could not find config file.");
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

    /**
     * @param mixed[] $config
     */
    protected function setStyler(array $config) : void
    {
        $styler = $config['styler'] ?? [];

        if ($styler instanceof Styler) {
            $this->styler = $styler;
        } elseif (is_array($styler)) {
            $this->styler = new Styler(...$styler);
        } else {
            throw new UnexpectedValueException(
                "Config key 'styler' misconfigured.",
            );
        }
    }

    protected function style(string $file) : string
    {
        /** @var string */
        $code = file_get_contents($file);

        /** @var Stmt[] */
        $stmts = $this->parser->parse($code);

        return $this->printer->printFile($stmts, $this->styler);
    }
}

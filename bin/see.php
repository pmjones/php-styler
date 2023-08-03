<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$file = $argv[1];
exec("php -l $file", $output, $return);

if ($return !== 0) {
    echo implode(PHP_EOL, $output) . PHP_EOL;
    exit($return);
}

// parse
$parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
$stmts = $parser->parse(file_get_contents($file));

/// print
$printer = new \PhpStyler\Printer();
echo $printer->printFile($stmts, new \PhpStyler\Styler());

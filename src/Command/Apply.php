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

    protected int $count = 0;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }

    public function __invoke(string $configFile) : int
    {
        $start = microtime(true);
        $this->count = 0;
        $this->printer = new Printer();

        // load config
        $config = $this->load($configFile);
        $cache = ['time' => filemtime($configFile)];

        // load cache
        $cacheFile = $config['cache'] ?? false;
        unset($config['cache']);

        if ($cacheFile && file_exists($cacheFile)) {
            $cache = $this->load($cacheFile);
        } else {
            $cache = ['time' => 0];
        }

        // get files to style
        $files = $config['files'] ?? [];
        unset($config['files']);

        // get the configured styler object
        $this->styler = $config['styler'] ?? null;
        unset($config['styler']);
        $this->styler ??= new Styler(...$config);

        // apply to files
        $exit = $this->apply($files, $cache['time']);

        if ($exit) {
            return $exit;
        }

        // save cache
        if ($cacheFile) {
            echo "Saving {$cacheFile}" . PHP_EOL;
            $data = '<?php return '
                . var_export(['time' => time()], true)
                . ';'
                . PHP_EOL
            ;
            file_put_contents($cacheFile, $data);
        }

        // report results
        echo "Styled {$this->count} files in "
            . number_format(microtime(true) - $start, 3)
            . ' seconds.'
            . PHP_EOL
        ;

        return 0;
    }

    protected function load(string $file) : array
    {
        echo "Loading " . $file . PHP_EOL;

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

    protected function apply(array $files, int $mtime) : int
    {
        foreach ($files as $file) {
            $file = (string) $file;

            if (is_dir($file) || filemtime($file) < $mtime) {
                continue;
            }

            $this->count ++;

            if (! $this->lint($file)) {
                return 1;
            }

            $this->replace($file);
        }

        return 0;
    }

    protected function replace(string $file) : void
    {
        $stmts = $this->parser->parse(file_get_contents($file));
        $code = $this->printer->printFile($stmts, $this->styler);
        file_put_contents($file, $code);
        echo $file . PHP_EOL;
    }
}

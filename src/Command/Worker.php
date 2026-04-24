<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use AutoShell\Help;
use PhpStyler\Styler;
use Throwable;

#[Help("Internal worker process for parallel styling.")]
class Worker extends ACommand
{
    public function __invoke(WorkerOptions $options) : int
    {
        $configFile = $options->configFile;

        if ($configFile === null) {
            $this->writeStderr("Worker requires --config" . PHP_EOL);
            return 1;
        }

        $mode = $options->mode;

        if ($mode === null || ! in_array($mode, ['apply', 'check', 'diff'])) {
            $this->writeStderr(
                "Worker requires --mode=apply|check|diff" . PHP_EOL,
            );

            return 1;
        }

        $config = $this->loadConfigFile($configFile);
        $styler = new Styler($config->format);
        $files = $this->readFilesFromStdin();
        $hasError = false;

        foreach ($files as $file) {
            if ($mode === 'apply') {
                $result = $this->applyFile($styler, $file);
            } elseif ($mode === 'check') {
                $result = $this->checkFile($styler, $file);
            } else {
                $result = $this->diffFile($styler, $file);
            }

            $this->writeStdout(
                json_encode($result, JSON_UNESCAPED_SLASHES) . "\n",
            );

            if (isset($result['ok']) && ! $result['ok']) {
                $hasError = true;
            }
        }

        return (int) $hasError;
    }

    // @codeCoverageIgnoreStart
    // thin STDIN/STDOUT/STDERR wrappers exist for test overrides; exercising
    // them in-process would require redirecting real FDs
    protected function writeStdout(string $message) : void
    {
        fwrite(STDOUT, $message);
    }

    protected function writeStderr(string $message) : void
    {
        fwrite(STDERR, $message);
    }

    /**
     * @return string[]
     */
    protected function readFilesFromStdin() : array
    {
        $input = (string) stream_get_contents(STDIN);
        $lines = explode("\n", trim($input));

        return array_filter($lines, fn (string $line) => $line !== '');
    }

    // @codeCoverageIgnoreEnd

    /**
     * @return array<string, mixed>
     */
    protected function applyFile(Styler $styler, string $file) : array
    {
        try {
            $code = $styler((string) file_get_contents($file));
            file_put_contents($file, $code);
            return ['file' => $file, 'ok' => true];
        } catch (Throwable $e) {
            return ['file' => $file, 'ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function checkFile(Styler $styler, string $file) : array
    {
        try {
            $source = (string) file_get_contents($file);
            $styled = $styler($source);
            return ['file' => $file, 'ok' => true, 'match' => $source === $styled];
        } catch (Throwable $e) {
            return ['file' => $file, 'ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function diffFile(Styler $styler, string $file) : array
    {
        try {
            $source = (string) file_get_contents($file);
            $styled = $styler($source);

            if ($source === $styled) {
                return ['file' => $file, 'ok' => true, 'diff' => ''];
            }

            $diff = $this->computeDiff($file, $styled);
            return ['file' => $file, 'ok' => true, 'diff' => $diff];
        } catch (Throwable $e) {
            return ['file' => $file, 'ok' => false, 'error' => $e->getMessage()];
        }
    }

    protected function computeDiff(string $file, string $styled) : string
    {
        $tempFile = (string) tempnam(sys_get_temp_dir(), 'php-styler-');

        try {
            file_put_contents($tempFile, $styled);

            $command = sprintf(
                'diff -u %s %s',
                escapeshellarg($file),
                escapeshellarg($tempFile),
            );

            $output = (string) shell_exec($command);
            return $output;
        } finally {
            unlink($tempFile);
        }
    }
}

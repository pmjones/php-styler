<?php
declare(strict_types=1);

namespace PhpStyler\Command;

class WorkerWithStdinOverride extends Worker
{
    public string $stderr = '';

    public string $stdout = '';

    /** @param string[] $lines */
    public function __construct(private array $lines)
    {
    }

    protected function writeStderr(string $message) : void
    {
        $this->stderr .= $message;
    }

    protected function writeStdout(string $message) : void
    {
        $this->stdout .= $message;
    }

    /** @return string[] */
    public function publicReadFilesFromStdin() : array
    {
        return $this->readFilesFromStdin();
    }

    /** @return string[] */
    protected function readFilesFromStdin() : array
    {
        $input = implode("\n", $this->lines);
        $out = explode("\n", trim($input));
        return array_filter($out, fn (string $line) => $line !== '');
    }
}

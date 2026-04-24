<?php
declare(strict_types=1);

namespace PhpStyler\Command;

use PhpStyler\Styler;

class WorkerSpy extends Worker
{
    public string $stderr = '';

    protected function writeStderr(string $message) : void
    {
        $this->stderr .= $message;
    }

    /** @return array<string, mixed> */
    public function publicApplyFile(Styler $styler, string $file) : array
    {
        return $this->applyFile($styler, $file);
    }

    /** @return array<string, mixed> */
    public function publicCheckFile(Styler $styler, string $file) : array
    {
        return $this->checkFile($styler, $file);
    }

    /** @return array<string, mixed> */
    public function publicDiffFile(Styler $styler, string $file) : array
    {
        return $this->diffFile($styler, $file);
    }
}

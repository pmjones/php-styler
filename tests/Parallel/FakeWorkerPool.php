<?php
declare(strict_types=1);

namespace PhpStyler\Parallel;

class FakeWorkerPool extends WorkerPool
{
    public function __construct(private string $fakeBin)
    {
    }

    protected function binPath() : string
    {
        return $this->fakeBin;
    }
}

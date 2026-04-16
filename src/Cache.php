<?php
declare(strict_types=1);

namespace PhpStyler;

class Cache
{
    /** @var array<string, string> file path => content hash */
    private array $fileHashes = [];

    public function __construct(
        private readonly string $cacheFile,
        private readonly string $configHash,
    ) {
    }

    public function load() : void
    {
        if (! file_exists($this->cacheFile)) {
            return;
        }

        $json = (string) file_get_contents($this->cacheFile);
        $data = json_decode($json, true);

        if (! is_array($data)) {
            return;
        }

        if (($data['configHash'] ?? '') !== $this->configHash) {
            return;
        }

        /** @var array<string, string> $fileHashes */
        $fileHashes = $data['fileHashes'] ?? [];
        $this->fileHashes = $fileHashes;
    }

    public function isCurrent(string $file) : bool
    {
        $stored = $this->fileHashes[$file] ?? null;

        if ($stored === null) {
            return false;
        }

        return $stored === (string) md5_file($file);
    }

    public function update(string $file) : void
    {
        $this->fileHashes[$file] = (string) md5_file($file);
    }

    public function delete(string $file) : void
    {
        unset($this->fileHashes[$file]);
    }

    public function clear() : void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        $this->fileHashes = [];
    }

    public function save() : void
    {
        $data = [
            'configHash' => $this->configHash,
            'fileHashes' => $this->fileHashes,
        ];

        file_put_contents(
            $this->cacheFile,
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n",
        );
    }
}

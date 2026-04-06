<?php
declare(strict_types=1);

namespace PhpStyler\Parallel;

class WorkerResult
{
    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data) : self
    {
        /** @var string */
        $file = $data['file'] ?? '';

        /** @var ?string */
        $error = $data['error'] ?? null;

        /** @var ?bool */
        $isMatch = isset($data['match']) ? (bool) $data['match'] : null;

        /** @var ?string */
        $diff = $data['diff'] ?? null;

        return new self(
            file: $file,
            ok: (bool) ($data['ok'] ?? true),
            error: $error,
            isMatch: $isMatch,
            diff: $diff,
        );
    }

    public function __construct(
        public readonly string $file,
        public readonly bool $ok = true,
        public readonly ?string $error = null,
        public readonly ?bool $isMatch = null,
        public readonly ?string $diff = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray() : array
    {
        $data = ['file' => $this->file, 'ok' => $this->ok];

        if ($this->error !== null) {
            $data['error'] = $this->error;
        }

        if ($this->isMatch !== null) {
            $data['match'] = $this->isMatch;
        }

        if ($this->diff !== null) {
            $data['diff'] = $this->diff;
        }

        return $data;
    }
}

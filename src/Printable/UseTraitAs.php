<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class UseTraitAs extends Printable
{
    public function __construct(
        public readonly ?string $trait,
        public readonly string $oldName,
        public readonly ?int $flags,
        public readonly ?string $newName,
    ) {
    }
}

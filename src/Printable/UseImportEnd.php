<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class UseImportEnd extends Printable
{
    public function __construct(
        public readonly ?string $prefix,
    ) {
    }
}

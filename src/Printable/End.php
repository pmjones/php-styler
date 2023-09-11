<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class End extends Printable
{
    public readonly string $type;

    public function __construct(public readonly Printable $orig)
    {
        $this->type = rtrim(substr((string) strrchr(get_class($orig), '\\'), 1), '_');
    }
}

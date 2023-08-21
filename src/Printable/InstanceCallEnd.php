<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class InstanceCallEnd extends Printable
{
    public function __construct(
        public readonly string $operator,
        public readonly int $fluentNum,
        public readonly int $fluentEnd,
    ) {
    }

    public function isFluent() : bool
    {
        return $this->fluentEnd > 1;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class InstanceOpEnd extends Printable
{
    public function __construct(
        public readonly string $str,
        public readonly mixed $fluentNum,
        public readonly mixed $fluentEnd,
    ) {
    }

    public function isFluent() : bool
    {
        return $this->fluentEnd > 1;
    }
}

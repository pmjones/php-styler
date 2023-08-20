<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class InstanceProp extends Printable
{
    public function __construct(
        public readonly string $operator,
        public readonly int $fluentNum,
        public readonly int $fluentEnd,
    ) {
    }

    public function isFluent()
    {
        return $this->fluentEnd > 1;
    }
}

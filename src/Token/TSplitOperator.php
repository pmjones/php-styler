<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitOperator extends TSplit
{
    private int $priority = 0;

    public static function new(int $priority) : static
    {
        $self = new static(T_WHITESPACE, '');
        $self->priority = $priority;
        return $self;
    }

    public function splitPriority() : int
    {
        return $this->priority;
    }
}

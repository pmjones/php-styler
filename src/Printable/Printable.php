<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

abstract class Printable
{
    protected bool $hasComment = false;

    protected bool $isFirst = false;

    public function hasComment(bool $hasComment = null) : ?bool
    {
        if ($hasComment === null) {
            return $this->hasComment;
        }

        $this->hasComment = $hasComment;

        return null;
    }

    public function isFirst(bool $isFirst = null) : ?bool
    {
        if ($isFirst === null) {
            return $this->isFirst;
        }

        $this->isFirst = $isFirst;

        return null;
    }
}

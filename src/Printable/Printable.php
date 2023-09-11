<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

abstract class Printable
{
    protected bool $hasAttribute = false;

    protected bool $hasComment = false;

    protected bool $isExpansive = false;

    protected bool $isFirst = false;

    public function hasAttribute(bool $hasAttribute = null) : ?bool
    {
        if ($hasAttribute === null) {
            return $this->hasAttribute;
        }

        $this->hasAttribute = $hasAttribute;
        return null;
    }

    public function hasComment(bool $hasComment = null) : ?bool
    {
        if ($hasComment === null) {
            return $this->hasComment;
        }

        $this->hasComment = $hasComment;
        return null;
    }

    public function isExpansive(bool $isExpansive = null) : ?bool
    {
        if ($isExpansive === null) {
            return $this->isExpansive;
        }

        $this->isExpansive = $isExpansive;
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

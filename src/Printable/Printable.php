<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

abstract class Printable
{
    protected bool $isExpansive = false;

    public function isExpansive(bool $isExpansive = null) : ?bool
    {
        if ($isExpansive === null) {
            return $this->isExpansive;
        }

        $this->isExpansive = $isExpansive;
        return null;
    }
}

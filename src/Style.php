<?php
declare(strict_types=1);

namespace PhpStyler;

class Style
{
    /**
     * @param ?callable(string):string $case
     */
    public function __construct(
        public readonly ?bool $spaceBefore = null,
        public readonly ?bool $spaceAfter = null,
        public readonly ?bool $lineBreakBefore = null,
        public readonly ?bool $lineBreakAfter = null,
        public readonly ?bool $blankLineBefore = null,
        public readonly ?bool $blankLineAfter = null,
        public readonly mixed $case = null,
    ) {
    }
}

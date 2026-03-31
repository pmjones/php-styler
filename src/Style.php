<?php
declare(strict_types=1);

namespace PhpStyler;

readonly class Style
{
    /**
     * @param ?callable(string):string $case
     */
    public function __construct(
        public ?bool $spaceBefore = null,
        public ?bool $spaceAfter = null,
        public ?bool $lineBreakBefore = null,
        public ?bool $lineBreakAfter = null,
        public ?bool $blankLineBefore = null,
        public ?bool $blankLineAfter = null,
        public mixed $case = null,
    ) {
    }
}

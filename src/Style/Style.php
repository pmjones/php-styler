<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class Style
{
    public ?bool $spaceBefore = null;

    public ?bool $spaceAfter = null;

    public ?bool $lineBreakBefore = null;

    public ?bool $lineBreakAfter = null;

    public ?bool $blankLineBefore = null;

    public ?bool $blankLineAfter = null;

    /**
     * @var ?callable(string):string
     */
    public mixed $case = null;

    /**
     * @param ?callable(string):string $case
     */
    public function __construct(
        ?bool $spaceBefore = null,
        ?bool $spaceAfter = null,
        ?bool $lineBreakBefore = null,
        ?bool $lineBreakAfter = null,
        ?bool $blankLineBefore = null,
        ?bool $blankLineAfter = null,
        mixed $case = null,
    ) {
        if ($spaceBefore !== null) {
            $this->spaceBefore = $spaceBefore;
        }

        if ($spaceAfter !== null) {
            $this->spaceAfter = $spaceAfter;
        }

        if ($lineBreakBefore !== null) {
            $this->lineBreakBefore = $lineBreakBefore;
        }

        if ($lineBreakAfter !== null) {
            $this->lineBreakAfter = $lineBreakAfter;
        }

        if ($blankLineBefore !== null) {
            $this->blankLineBefore = $blankLineBefore;
        }

        if ($blankLineAfter !== null) {
            $this->blankLineAfter = $blankLineAfter;
        }

        if ($case !== null) {
            $this->case = $case;
        }
    }
}

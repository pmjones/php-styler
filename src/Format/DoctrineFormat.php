<?php
declare(strict_types=1);

namespace PhpStyler\Format;

class DoctrineFormat extends ExtendedFormat
{
    /**
     * @inheritdoc
     */
    public function __construct(
        string $eol = "\n",
        int $lineLen = 120,
        int $indentLen = 4,
        bool $indentTab = false,
        array $styles = [],
        array $rules = [],
    ) {
        parent::__construct(
            eol: $eol,
            lineLen: $lineLen,
            indentLen: $indentLen,
            indentTab: $indentTab,
            styles: $styles,
            rules: $rules,
        );
        $this->setConcatenationSpacing(false);
        $this->setReturnTypeColonSpacing(false);
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;

class LineFactory
{
    public readonly string $indentStr;

    public function __construct(
        public readonly ?int $lineLen = 88,
        public readonly int $indentLen = 4,
        bool $indentTab = false,
    ) {
        $this->indentStr = $indentTab ? "\t" : str_repeat(' ', $indentLen);
    }

    /**
     * @param AToken[] $tokens
     */
    public function new(array $tokens = [], int $indent = 0) : Line
    {
        return new Line($tokens, $indent, $this->indentStr, $this->indentLen);
    }
}

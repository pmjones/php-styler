<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;

class Config
{
    /**
     * @param string[] $files
     * @param array<TokenRule|LineRule> $rules
     */
    public function __construct(
        public readonly iterable $files,
        public readonly ?string $cache,
        public readonly string $eol = "\n",
        public readonly int $lineLen = 88,
        public readonly int $indentLen = 4,
        public readonly bool $indentTab = false,
        public readonly array $rules = [],
    ) {
    }
}

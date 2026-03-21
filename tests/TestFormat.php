<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;

class TestFormat extends DeclarationFormat
{
    public protected(set) array $rules = [];

    /**
     * @param array<class-string<\PhpStyler\Token\AToken>, class-string<\PhpStyler\Token\AToken>> $parses
     */
    public function __construct(
        int $lineLen = 88,
        array $rules = [],
        array $parses = [],
    ) {
        parent::__construct(
            lineLen: $lineLen,
            rules: $rules,
            parses: $parses,
        );
    }
}

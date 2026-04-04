<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Token;

class TestFormat extends DeclarationFormat
{
    public protected(set) array $parseAs = [
        Token\TElse::class => Token\TElseAsElseIf::class,
    ];

    public protected(set) array $rules = [];

    /**
     * @param array<class-string<\PhpStyler\Token\AToken>, class-string<\PhpStyler\Token\AToken>> $parseAs
     */
    public function __construct(
        int $lineLen = 88,
        array $rules = [],
        array $parseAs = [],
    ) {
        parent::__construct(
            lineLen: $lineLen,
            rules: $rules,
            parseAs: $parseAs,
        );
    }
}

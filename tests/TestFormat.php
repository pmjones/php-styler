<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Token;

class TestFormat extends DeclarationFormat
{
    public protected(set) array $parses = [
        Token\TList::class => Token\TListAsArray::class,
        Token\TArray::class => Token\TArrayAsShort::class,
        Token\TElse::class => Token\TElseAsElseIf::class,
    ];

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

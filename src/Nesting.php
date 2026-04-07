<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;

class Nesting
{
    public readonly string $class;

    /** @var ?class-string<AToken> */
    public readonly ?string $openingBrace;

    /** @var ?class-string<AToken> */
    public readonly ?string $closingBrace;

    /** @var ?class-string<AToken> */
    public readonly ?string $endSemicolon;

    public function __construct(
        public readonly AToken $token,
        public int $argCount = 0,
    ) {
        $this->class = get_class($token);
        $this->openingBrace = $token::OPENING_BRACE;
        $this->closingBrace = $token::CLOSING_BRACE;
        $this->endSemicolon = $token::END_SEMICOLON;
    }
}

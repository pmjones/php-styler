<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TArrayClosingBracket;
use PhpStyler\Token\TArrayOpeningBracket;
use PhpStyler\Token\TList;

class ConvertToShortListSyntax extends ATokenRule
{
    /** @var list<AToken|null> closing-paren tokens that should become ] */
    private array $closerStack = [];

    private bool $pendingList = false;

    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $this->closerStack = [];
        $this->pendingList = false;
        $result = [];

        foreach ($tokens as $token) {
            if ($token instanceof TList) {
                $this->pendingList = true;
                continue;
            }

            if ($this->pendingList && $token instanceof TArgsOpeningParen) {
                $this->pendingList = false;
                $this->closerStack[] = $token->closingToken;
                $result[] = $this->openBracket($token);
                continue;
            }

            if ($this->closerStack !== [] && $token === end($this->closerStack)) {
                array_pop($this->closerStack);
                $result[] = $this->closeBracket($token);
                continue;
            }

            $this->pendingList = false;
            $result[] = $token;
        }

        return $result;
    }

    private function openBracket(AToken $paren) : TArrayOpeningBracket
    {
        $bracket = new TArrayOpeningBracket(AToken::SYNTHETIC, '[');
        $bracket->closingToken = $paren->closingToken;
        return $bracket;
    }

    private function closeBracket(AToken $paren) : TArrayClosingBracket
    {
        $bracket = new TArrayClosingBracket(AToken::SYNTHETIC, ']');
        $bracket->openingToken = $paren->openingToken;
        return $bracket;
    }
}

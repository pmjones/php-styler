<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;

class Assembler
{
    /**
     * @var Line[]
     */
    private array $lines = [];

    private int $indent = 0;

    private Line $line;

    public function __construct(private LineFactory $lineFactory = new LineFactory())
    {
    }

    /**
     * @param AToken[] $tokens
     * @return Line[]
     */
    public function assemble(array $tokens) : array
    {
        $this->lines = [];
        $this->indent = 0;
        $this->line = $this->lineFactory->new();

        foreach ($tokens as $token) {
            $this->assembleToken($token);
        }

        $this->flushLine();
        return $this->lines;
    }

    private function assembleToken(AToken $token) : void
    {
        if ($token instanceof TIndentIncrement) {
            $this->indent ++;
            return;
        }

        if ($token instanceof TIndentDecrement) {
            $this->indent --;
            return;
        }

        if ($token instanceof TLineBreak) {
            $this->flushLine();
            return;
        }

        if (! $this->line->hasTokens()) {
            $this->line->indent = $this->indent;
        }

        $this->line->addToken($token);
    }

    private function flushLine() : void
    {
        if (! $this->line->hasTokens()) {
            return;
        }

        $this->addLine($this->line);
        $this->line = $this->lineFactory->new();
    }

    private function addLine(Line $line) : void
    {
        if ($line->isBlank() && $this->lines === []) {
            return;
        }

        $this->lines[] = $line;
    }
}

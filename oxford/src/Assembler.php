<?php
declare(strict_types=1);

namespace Oxford;

use Oxford\Token\T;
use Oxford\Token\TIndentDecrement;
use Oxford\Token\TIndentIncrement;
use Oxford\Token\TLineBreak;

class Assembler
{
    /** @var Line[] */
    private array $lines = [];

    private int $indent = 0;

    private Line $line;

    public function __construct(
        private LineFactory $lineFactory = new LineFactory(),
    ) {
    }

    /**
     * @param T[] $tokens
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

        while ($this->lines !== [] && end($this->lines)->isBlank()) {
            array_pop($this->lines);
        }

        return $this->lines;
    }

    private function assembleToken(T $token) : void
    {
        if ($token instanceof TIndentIncrement) {
            $this->indent++;
            return;
        }

        if ($token instanceof TIndentDecrement) {
            $this->indent--;
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

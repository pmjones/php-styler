<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Style\StyleLocator;

class Styler
{
    private Parser $parser;

    private Assembler $assembler;

    private Splitter $splitter;

    /**
     * @var TokenRule[]
     */
    private array $tokenRules = [];

    /**
     * @var LineRule[]
     */
    private array $lineRules = [];

    public static function fromConfig(Config $config) : self
    {
        return new self(
            eol: $config->eol,
            lineLen: $config->lineLen,
            indentLen: $config->indentLen,
            indentTab: $config->indentTab,
            rules: $config->rules,
        );
    }

    /**
     * @param array<TokenRule|LineRule> $rules
     */
    public function __construct(
        private string $eol = "\n",
        int $lineLen = 88,
        int $indentLen = 4,
        bool $indentTab = false,
        ?StyleLocator $styles = null,
        array $rules = [],
    ) {
        $lineFactory = new LineFactory($lineLen, $indentLen, $indentTab);
        $this->parser = new Parser($styles);
        $this->assembler = new Assembler($lineFactory);
        $this->splitter = new Splitter($lineFactory);

        foreach ($rules as $rule) {
            if ($rule instanceof TokenRule) {
                $this->tokenRules[] = $rule;
            }

            if ($rule instanceof LineRule) {
                $this->lineRules[] = $rule;
            }
        }
    }

    public function __invoke(string $code) : string
    {
        $tokens = $this->parse($code);
        $tokens = $this->applyTokenRules($tokens);
        $lines = $this->assemble($tokens);
        $lines = $this->split($lines);
        $lines = $this->applyLineRules($lines);
        return $this->render($lines);
    }

    /**
     * @param Line[] $lines
     */
    public function render(array $lines) : string
    {
        while ($lines !== [] && end($lines)->isBlank()) {
            array_pop($lines);
        }

        $rendered = [];

        foreach ($lines as $line) {
            $rendered[] = $line->render();
        }

        return implode($this->eol, $rendered) . $this->eol;
    }

    /**
     * @return Token\T[]
     */
    public function parse(string $code) : array
    {
        return ($this->parser)($code);
    }

    /**
     * @param Token\T[] $tokens
     * @return Token\T[]
     */
    private function applyTokenRules(array $tokens) : array
    {
        foreach ($this->tokenRules as $rule) {
            $tokens = $rule->apply($tokens);
        }

        return $tokens;
    }

    /**
     * @param Token\T[] $tokens
     * @return Line[]
     */
    public function assemble(array $tokens) : array
    {
        return $this->assembler->assemble($tokens);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function split(array $lines) : array
    {
        return $this->splitter->split($lines);
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    private function applyLineRules(array $lines) : array
    {
        foreach ($this->lineRules as $rule) {
            $lines = $rule->apply($lines);
        }

        return $lines;
    }
}

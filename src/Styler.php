<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\Format;
use PhpStyler\Format\PlainFormat;
use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;

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
        return new self($config->format);
    }

    public function __construct(private Format $format = new PlainFormat())
    {
        $lineFactory = new LineFactory(
            $format->lineLen,
            $format->indentLen,
            $format->indentTab,
        );
        $this->parser = new Parser($format);
        $this->assembler = new Assembler($lineFactory);
        $this->splitter = new Splitter($lineFactory);

        foreach ($format->rules as $class => $args) {
            $rule = new $class(...$args);

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
        $rendered = [];

        foreach ($lines as $line) {
            $rendered[] = $line->render();
        }

        return implode($this->format->eol, $rendered) . $this->format->eol;
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

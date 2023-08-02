<?php
declare(strict_types=1);

namespace PhpStyler;

use BadMethodCallException;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;
use ArrayObject;

class Code extends ArrayObject
{
    public const SPLIT_RULE_ARGS = 'SPLIT_RULE_ARGS';

    public const SPLIT_RULE_ARRAY = 'SPLIT_RULE_ARRAY';

    public const SPLIT_RULE_CONCAT = 'SPLIT_RULE_CONCAT';

    public const SPLIT_RULE_CONDITIONS = 'SPLIT_RULE_CONDITIONS';

    public const SPLIT_RULE_FLUENT = 'SPLIT_RULE_FLUENT';

    public const SPLIT_RULE_TERNARY = 'SPLIT_RULE_TERNARY';

    public const SPLIT_RULE_PARAMS = 'SPLIT_RULE_PARAMS';

    protected string $file = '';

    protected string $indent = '';

    protected string $lines = '';

    protected bool $multiline = false;

    protected array $splitRuleSet = [];

    protected bool $forceSplit = false;

    public function __construct(
        protected string $eol = "\n",
        protected int $maxlen = 80
    ) {
        parent::__construct([]);
    }

    public function getFile() : string
    {
        return rtrim($this->file) . $this->eol;
    }

    public function done() : void
    {
        $oldIndent = $this->indent;
        $splitRules = [
            static::SPLIT_RULE_TERNARY,
            static::SPLIT_RULE_ARRAY . "_0",
            static::SPLIT_RULE_ARRAY . "_1",
            static::SPLIT_RULE_ARRAY . "_2",
            static::SPLIT_RULE_ARRAY . "_3",
            static::SPLIT_RULE_ARRAY . "_4",
            static::SPLIT_RULE_ARRAY . "_5",
            static::SPLIT_RULE_CONDITIONS,
            static::SPLIT_RULE_CONCAT,
            static::SPLIT_RULE_ARGS . "_0",
            static::SPLIT_RULE_ARGS . "_1",
            static::SPLIT_RULE_ARGS . "_2",
            static::SPLIT_RULE_ARGS . "_3",
            static::SPLIT_RULE_ARGS . "_4",
            static::SPLIT_RULE_ARGS . "_5",
            static::SPLIT_RULE_FLUENT,
            static::SPLIT_RULE_PARAMS,
        ];
        $this->splitRuleSet = [];
        $this->setLines();
        $this->multiline = true;

        while ($this->atLeastOneLineTooLong() && $splitRules) {
            $this->indent = $oldIndent;
            $this->splitRuleSet[] = array_shift($splitRules);
            $this->setLines();
        }

        $this->multiline = false;

        // retain in file and reset for next round
        $this->file .= $this->lines;
        $this->exchangeArray([$this->eol . $this->indent]);
    }

    protected function atLeastOneLineTooLong() : bool
    {
        if ($this->forceSplit) {
            $this->forceSplit = false;
            return true;
        }

        foreach (explode($this->eol, $this->lines) as $line) {
            if (strlen($line) > $this->maxlen) {
                return true;
            }
        }

        return false;
    }

    protected function setLines() : void
    {
        $this->lines = '';

        foreach ($this as $part) {
            if (is_array($part)) {
                $method = array_shift($part);
                $this->{$method}(...$part);
            } else {
                $this->lines .= $part;
            }
        }

        $this->lines = preg_replace("/\s+$/m", "\n", $this->lines);
    }

    protected function newline() : void
    {
        $this->lines .= $this->eol . $this->indent;
    }

    protected function cuddle() : void
    {
        $this->file = rtrim($this->file);
        $this->lines = rtrim($this->lines) . $this->eol . $this->indent;
    }

    protected function cuddleParen() : void
    {
        $trimmed = rtrim($this->lines);
        $lines = explode($this->eol, $trimmed);
        $last = end($lines);

        if (trim($last) === ')') {
            $this->lines = $trimmed . ' ';
        }
    }

    protected function indent() : void
    {
        $this->indent .= '    ';
    }

    protected function outdent() : void
    {
        $this->indent = substr($this->indent, 0, -4);
    }

    protected function forceSplit() : void
    {
        $this->forceSplit = true;
    }

    protected function split(
        string $splitRule,
        string $type = '',
        string ...$args
    ) : void
    {
        if (! $this->multiline) {
            return;
        }

        if (! in_array($splitRule, $this->splitRuleSet)) {
            return;
        }

        switch ($type) {
            case '':
                $this->indent();
                $this->newline();
                break;

            case 'mid':
                $this->lines = rtrim($this->lines);
                $this->newline();
                break;

            case 'end':
                $this->lines .= $args[0] ?? '';
                $this->outdent();
                $this->newline();
                break;

            case 'cuddle':
                $this->indent();
                $this->cuddle();
                break;

            case 'endCuddle':
                $this->outdent();
                $this->cuddle();
                break;

            default:
        }
    }
}

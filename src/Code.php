<?php
declare(strict_types=1);

namespace PhpStyler;

use BadMethodCallException;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;
use ArrayObject;

class Code extends ArrayObject
{
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
            Expr\Ternary::class,
            Expr\BinaryOp\Concat::class,
            P\Array::class . "_0",
            P\Array::class . "_1",
            P\Array::class . "_2",
            P\Array::class . "_3",
            P\Array::class . "_4",
            P\Array::class . "_5",
            P\Cond::class,
            P\Precedence::class,
            Expr\BinaryOp\BooleanOr::class,
            Expr\BinaryOp\BooleanAnd::class,
            P\Args::class . "_0",
            P\Args::class . "_1",
            P\Args::class . "_2",
            P\Args::class . "_3",
            P\Args::class . "_4",
            P\Args::class . "_5",
            BinaryOp\Coalesce::class,
            P\MethodCall::class,
            P\Params::class,
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

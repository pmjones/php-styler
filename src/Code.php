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
    public const SPLIT = [
        P\Args::class => 'args',
        P\Array::class => 'array',
        P\Cond::class => 'cond',
        Expr\BinaryOp\BooleanAnd::class => 'bool_and',
        Expr\BinaryOp\BooleanOr::class => 'bool_or',
        Expr\BinaryOp\Coalesce::class => 'coalesce',
        Expr\BinaryOp\Concat::class => 'concat',
        P\Member::class => 'member',
        P\Params::class => 'params',
        P\Precedence::class => 'precedence',
        Expr\Ternary::class => 'ternary',
    ];

    protected string $file = '';

    protected string $lines = '';

    protected bool $multiline = false;

    protected array $splitApply = [];

    protected array $splitCalls = [];

    protected bool $forceSplit = false;

    protected string $indent = '';

    public function __construct(
        protected string $eol = "\n",
        protected int $lineLen = 80,
        protected string $indentStr = "    ",
        protected int $indentLen = 0,
        protected array $splitOrder = [
            'concat',
            'array_0',
            'array_1',
            'array_2',
            'array_3',
            'array_4',
            'array_5',
            'ternary',
            'cond',
            'bool_and',
            'precedence',
            'bool_or',
            'member_0',
            'args_0',
            'member_1',
            'args_1',
            'member_2',
            'args_2',
            'member_3',
            'args_3',
            'member_4',
            'args_4',
            'member_5',
            'args_5',
            'coalesce',
            'params',
        ],
    ) {
        parent::__construct([]);

        if (! $this->indentLen) {
            $this->indentLen = $this->indentStr === "\t"
                ? 4
                : strlen($indentStr)
            ;
        }
    }

    public function getFile() : string
    {
        return rtrim($this->file) . $this->eol;
    }

    public function split(
        string $class,
        int $level = null,
        string $type = null,
        ...$args,
    ) : void
    {
        $rule = Code::SPLIT[$class];
        $this[] = ['applySplit', $rule, $level, $type, ...$args];
        $key = $rule . ($level !== null ? "_{$level}" : '');
        $this->splitCalls[$key] = true;
    }

    public function done() : void
    {
        $oldIndent = $this->indent;
        $splitOrder = $this->splitOrder;
        $this->splitApply = [];
        $this->setLines();
        $this->multiline = true;

        while ($this->atLeastOneLineTooLong() && $splitOrder) {
            $this->indent = $oldIndent;
            $rule = array_shift($splitOrder);

            if ($this->splitCalls[$rule] ?? false) {
                $this->splitApply[] = $rule;
                $this->setLines();
            }
        }

        $this->multiline = false;

        // retain in file and reset for next round
        $this->file .= $this->lines;
        $this->splitCalls = [];
        $this->exchangeArray([$this->eol . $this->indent]);
    }

    protected function atLeastOneLineTooLong() : bool
    {
        if ($this->forceSplit) {
            $this->forceSplit = false;

            return true;
        }

        foreach (explode($this->eol, $this->lines) as $line) {
            if (strlen($line) > $this->lineLen) {
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

        $this->lines = preg_replace("/\\s+\$/m", "\n", $this->lines);
    }

    protected function newline() : void
    {
        $this->lines .= $this->eol . $this->indent;
    }

    protected function cuddle() : void
    {
        $trimmed = rtrim($this->lines);

        if ($trimmed === '') {
            $this->file = rtrim($this->file);
        }

        $this->lines = $trimmed . $this->eol . $this->indent;
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
        $this->indent .= $this->indentStr;
    }

    protected function outdent() : void
    {
        $this->indent = substr($this->indent, 0, -1 * strlen($this->indentStr));
    }

    protected function forceSplit() : void
    {
        $this->forceSplit = true;
    }

    protected function applySplit(
        string $rule,
        ?int $level,
        ?string $type,
        string ...$args,
    ) : void
    {
        if (! $this->multiline) {
            return;
        }

        if ($level !== null) {
            $rule .= "_{$level}";
        }

        if (! in_array($rule, $this->splitApply)) {
            return;
        }

        switch ($type) {
            case null:
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

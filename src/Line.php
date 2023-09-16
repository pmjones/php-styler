<?php
declare(strict_types=1);

namespace PhpStyler;

use ArrayAccess;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;

/**
 * @implements ArrayAccess<int, mixed>
 */
class Line implements ArrayAccess
{
    protected const RULES = [
        P\Implements_::class,
        P\ArrowFunction::class,
        Expr\BinaryOp\Concat::class,
        P\Cond::class,
        P\Precedence::class,
        Expr\Ternary::class,
        Expr\BinaryOp\BooleanOr::class,
        Expr\BinaryOp\LogicalOr::class,
        Expr\BinaryOp\BooleanAnd::class,
        Expr\BinaryOp\LogicalAnd::class,
        P\Array_::class,
        P\Args::class,
        Expr\BinaryOp\Coalesce::class,
        P\MemberOp::class,
        P\Params::class,
    ];

    protected string $append = '';

    protected string $indent = '';

    /**
     * @var mixed[]
     */
    protected array $parts = [];

    protected Line $line;

    /**
     * @var Line[]
     */
    protected array $lines = [];

    public function __construct(
        protected string $eol,
        protected int $indentNum,
        protected int $indentLen,
        protected bool $indentTab,
        protected int $lineLen,
    ) {
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        if ($offset !== null) {
            throw new Exception(__CLASS__ . ' is append-only.');
        }

        $this->parts[] = $value;
    }

    public function offsetGet(mixed $offset) : mixed
    {
        throw new Exception(__CLASS__ . ' is write-only.');
    }

    public function offsetExists(mixed $offset) : bool
    {
        return isset($this->parts[$offset]);
    }

    public function offsetUnset(mixed $offset) : void
    {
        throw new Exception(__CLASS__ . ' is append-only.');
    }

    public function indent() : void
    {
        $this->indentNum ++;
    }

    public function outdent() : void
    {
        $this->indentNum --;
    }

    public function append(string &$output) : void
    {
        list($level, $rule) = $this->listLevelRule();

        if ($this->fitsOnSingleLine($output) || ! $rule) {
            $output .= rtrim($this->append) . $this->eol;
            return;
        }

        $this->splitLines($output, $level, $rule);
    }

    protected function splitLines(string &$output, int $level, string $rule) : void
    {
        $this->lines = [];
        $this->line = $this->newline();

        foreach ($this->parts as $part) {
            if (
                $part instanceof Split
                && $part->level === $level
                && $part->rule === $rule
            ) {
                $method = lcfirst($part->type . 'Split');
                $this->{$method}($part);
            } else {
                $this->line[] = $part;
            }
        }

        if ($this->line->parts) {
            $this->lines[] = $this->line;
        }

        foreach ($this->lines as $line) {
            $line->append($output);
        }
    }

    protected function newline() : Line
    {
        return new Line(
            $this->eol,
            $this->indentNum,
            $this->indentLen,
            $this->indentTab,
            $this->lineLen,
        );
    }

    protected function incrSplit(Split $part) : void
    {
        $this->lines[] = $this->line;
        $this->line = $this->newline();
        $this->line->indentNum ++;
    }

    protected function clipSplit(Split $part) : void
    {
        $this->lines[] = $this->line;
        $this->line = $this->newline();
        $this->line->indentNum ++;
        $this->line[] = new Clip();
    }

    protected function lastSplit(Split $part) : void
    {
        $this->line[] = ',';
        $this->lines[] = $this->line;
        $this->line = $this->newline();
    }

    protected function sameSplit(Split $part) : void
    {
        $this->lines[] = $this->line;
        $this->line = $this->newline();
    }

    protected function fitsOnSingleLine(string &$output) : bool
    {
        $indentStr = $this->indentTab ? "\t" : str_pad('', $this->indentLen);
        $this->append = str_repeat($indentStr, $this->indentNum);
        $oldOutput = $output;

        foreach ($this->parts as $part) {
            if ($part instanceof Clip) {
                $this->clip($part, $output);
            } elseif (is_string($part)) {
                $this->append .= $part;
            }
        }

        if (strlen($this->append) <= $this->lineLen) {
            return true;
        }

        $output = $oldOutput;
        return false;
    }

    /**
     * @return array{int, string}
     */
    protected function listLevelRule() : array
    {
        $rules = [];

        foreach ($this->parts as $part) {
            if ($part instanceof Split) {
                if (! in_array($part->rule, static::RULES)) {
                    throw new Exception("No such split rule: {$part->rule}");
                }

                $rules[$part->level][] = $part->rule;
            }
        }

        if (! $rules) {
            return [0, ''];
        }

        // get the highest-priority rule at the earliest level
        ksort($rules);
        $level = key($rules);
        $rules = current($rules);
        $rules = array_intersect(static::RULES, $rules);
        $rule = current($rules);
        return [$level, $rule];
    }

    protected function clip(Clip $clip, string &$output) : void
    {
        if (! $clip->toParen) {
            $this->append = ltrim($this->append);
            $output = rtrim($output);
            return;
        }

        // clips the line **only if** the last character
        // is a paren on **its own line**.
        $trimmed = rtrim($output);
        $exploded = explode(PHP_EOL, $trimmed);
        $last = end($exploded);

        if (trim($last) === ')') {
            $this->append = ltrim($this->append);
            $output = $trimmed . ' ';
        }
    }
}

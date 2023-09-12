<?php
declare(strict_types=1);

namespace PhpStyler;

use RuntimeException;
use ArrayAccess;

/**
 * @implements ArrayAccess<int, mixed>
 */
class Line implements ArrayAccess
{
    protected const RULES = [
        'implements',
        'cond',
        'precedence',
        'ternary',
        'or',
        'and',
        'coalesce',
        'instance_op_0',
        'array_0',
        'args_0',
        'instance_op_1',
        'array_1',
        'args_1',
        'instance_op_2',
        'array_2',
        'args_2',
        'instance_op_3',
        'instance_op_4',
        'array_3',
        'args_3',
        'array_4',
        'args_4',
        'instance_op_5',
        'array_5',
        'args_5',
        'concat',
        'params_0',
        'params_1',
        'params_2',
        'params_3',
        'params_4',
        'params_5',
        'attribute_args_0',
        'attribute_args_1',
        'attribute_args_2',
        'attribute_args_3',
        'attribute_args_4',
        'attribute_args_5',
    ];

    /**
     * @var mixed[]
     */
    protected array $parts = [];

    protected string $indent = '';

    protected string $append = '';

    /**
     * @var string[]
     */
    protected array $rules = [];

    /**
     * @var Line[]
     */
    protected array $lines = [];

    protected Line $line;

    protected bool $force = false;

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
        if ($offset === null) {
            $this->parts[] = $value;
        } else {
            $this->parts[$offset] = $value;
        }
    }

    public function offsetGet(mixed $offset) : mixed
    {
        return $this->parts[$offset];
    }

    public function offsetExists(mixed $offset) : bool
    {
        return isset($this->parts[$offset]);
    }

    public function offsetUnset(mixed $offset) : void
    {
        unset($this->parts[$offset]);
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
        $this->findRules();

        if ($this->force) {
            $this->splitLines($output, (string) reset($this->rules));
            return;
        }

        if ($this->fitsOnSingleLine($output) || ! $this->rules) {
            $output .= rtrim($this->append) . $this->eol;
            return;
        }

        $this->splitLines($output, reset($this->rules));
    }

    protected function splitLines(string &$output, string $rule) : void
    {
        $this->lines = [];
        $this->line = $this->newline();

        foreach ($this->parts as $part) {
            if ($part instanceof Space\Split && $part->rule === $rule) {
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

    protected function split(Space\Split $part) : void
    {
        $this->sameSplit($part);
        $this->line->indentNum ++;
    }

    protected function clipSplit(Space\Split $part) : void
    {
        $this->split($part);
        $this->line[] = new Space\Clip();
    }

    protected function sameSplit(Space\Split $part) : void
    {
        $this->lines[] = $this->line;
        $this->line = $this->newline();
    }

    protected function endSplit(Space\Split $part) : void
    {
        $this->line[] = $part->args[0] ?? '';
        $this->sameSplit($part);
    }

    protected function fitsOnSingleLine(string &$output) : bool
    {
        $indentStr = $this->indentTab ? "\t" : str_pad('', $this->indentLen);
        $this->append = str_repeat($indentStr, $this->indentNum);
        $oldOutput = $output;

        foreach ($this->parts as $part) {
            if ($part instanceof Space\Space) {
                $this->{$part->method}($output);
                continue;
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

    protected function findRules() : void
    {
        $this->force = false;
        $rules = [];

        foreach ($this->parts as $part) {
            if ($part instanceof Space\Split) {
                $rules[] = $part->rule;
            }
        }

        $this->rules = array_intersect(self::RULES, $rules);
    }

    protected function clip(string &$output) : void
    {
        $this->append = ltrim($this->append);
        $output = rtrim($output);
    }

    // clips the line **only if** the last character
    // is a paren on **its own line**.
    protected function clipToParen(string &$output) : void
    {
        $trimmed = rtrim($output);
        $exploded = explode(PHP_EOL, $trimmed);
        $last = end($exploded);

        if (trim($last) === ')') {
            $this->append = ltrim($this->append);
            $output = $trimmed . ' ';
        }
    }
}

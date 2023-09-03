<?php
declare(strict_types=1);

namespace PhpStyler;

use ArrayAccess;
use RuntimeException;

/**
 * @implements ArrayAccess<int, mixed>
 */
class Code implements ArrayAccess
{
    /**
     * @var mixed[]
     */
    protected array $parts = [];

    protected string $output = '';

    protected string $indentStr = '';

    protected string $indent = '';

    /**
     * @var string[]
     */
    protected array $rules = [
        'cond',
        'args_-1',
        'args_-2',
        'args_-3',
        'args_-4',
        'args_-5',
        'coalesce',
        'ternary',
        'concat',
        'implements',
        'precedence',
        'or',
        'and',
        'instance_op_0',
        'instance_op_1',
        'instance_op_2',
        'instance_op_3',
        'instance_op_4',
        'instance_op_5',
        'args_0',
        'args_1',
        'args_2',
        'args_3',
        'args_4',
        'args_5',
        'params_0',
        'params_1',
        'params_2',
        'params_3',
        'params_4',
        'params_5',
        'array_0',
        'array_1',
        'array_2',
        'array_3',
        'array_4',
        'array_5',
        'attribute_args_0',
        'attribute_args_1',
        'attribute_args_2',
        'attribute_args_3',
        'attribute_args_4',
        'attribute_args_5',
    ];

    /**
     * @param non-empty-string $eol
     */
    public function __construct(
        protected string $eol,
        protected int $lineLen,
        protected int $indentLen,
        bool $indentTab,
    ) {
        $this->indentStr = $indentTab ? "\t" : str_pad('', $this->indentLen);
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

    public function __invoke() : string
    {
        $this->indent = '';
        $this->output = '';
        $this->render($this->parts);
        $this->parts = [];
        return $this->output;
    }

    protected function render(array $parts) : void
    {
        $chunk = [];

        foreach ($parts as $part) {
            $chunk[] = $part;

            if ($part instanceof Space\Newline) {
                $this->append($chunk);
                $chunk = [];
            }
        }
    }

    protected function append(array $chunk) : void
    {
        $oldIndent = $this->indent;
        $force = [];
        $rules = [];

        foreach ($chunk as $part) {
            if (! $part instanceof Space\Split) {
                continue;
            }

            if ($part instanceof Space\Force) {
                $force[$part->rule] = true;
            }

            $rules[$part->rule] = true;
        }

        $rules = array_intersect($this->rules, array_keys($rules));
        $rule = reset($rules);

        if ($force[$rule] ?? false) {
            $this->split($oldIndent, $rule, $chunk);
            return;
        }

        $lines = $this->combine($chunk);

        if (! $rules) {
            $this->output .= $lines;
            return;
        }

        if ($this->tooLong($lines)) {
            $this->split($oldIndent, $rule, $chunk);
            return;
        }

        if (! $force) {
            $this->output .= $lines;
            return;
        }

        $force = array_intersect($this->rules, array_keys($force));
        $rule = reset($force);
        $this->split($oldIndent, $rule, $chunk);
    }

    protected function tooLong(string $lines)
    {
        $exploded = explode($this->eol, $lines);

        foreach ($exploded as $line) {
            if (strlen($line) > $this->lineLen) {
                return true;
            }
        }

        return false;
    }

    protected function split(string $oldIndent, string $rule, array $old) : void
    {
        $this->indent = $oldIndent;
        $new = [];

        foreach ($old as $part) {
            $split = $part instanceof Space\Split && $part->rule === $rule;

            if (! $split) {
                $new[] = $part;
                continue;
            }

            switch ($part->type) {
                case null:
                    $new[] = new Space\Indent();
                    $new[] = new Space\Newline();
                    break;

                case 'mid':
                    $new[] = new Space\Clip();
                    $new[] = new Space\Newline();
                    break;

                case 'end':
                    $new[] = $part->args[0] ?? '';
                    $new[] = new Space\Outdent();
                    $new[] = new Space\Newline();
                    break;

                case 'condense':
                    $new[] = new Space\Clip();
                    $new[] = new Space\Indent();
                    $new[] = new Space\Newline();
                    break;

                case 'endCondense':
                    $new[] = new Space\Clip();
                    $new[] = new Space\Outdent();
                    $new[] = new Space\Newline();
                    break;

                case 'outdent':
                    $new[] = new Space\Outdent();
                    break;

                default:
                    throw new RuntimeException("No such split: '{$part->type}'");
            }
        }

        $this->render($new);
    }

    protected function combine(array $chunk) : string
    {
        $lines = $this->indent;

        foreach ($chunk as $part) {
            if (is_string($part)) {
                $lines .= $part;
            } elseif ($part instanceof Space\Space) {
                $this->{$part->method}($lines);
            }
        }

        $lines = (string) preg_replace("/\\s+\$/m", "\n", $lines);
        return $lines;
    }

    protected function newline(string &$lines) : void
    {
        $lines .= $this->eol . $this->indent;
    }

    protected function midline(string &$lines) : void
    {
        $lines .= $this->eol . $this->indent;
    }

    protected function clip(string &$lines) : void
    {
        $lines = rtrim($lines);

        if ($lines === '') {
            $this->output = rtrim($this->output);
        }
    }

    // clips the line **only if** the last character is a paren on **its own line**.
    protected function clipToParen(string &$lines) : void
    {
        if (rtrim($lines) === '') {
            $trimmed = rtrim($this->output);
            $exploded = explode($this->eol, $trimmed);
            $last = end($exploded);

            if (trim($last) === ')') {
                $this->output = $trimmed . ' ';
                $lines = rtrim($lines);
                return;
            }
        }

        $trimmed = rtrim($lines);
        $exploded = explode($this->eol, $trimmed);
        $last = end($exploded);

        if (trim($last) === ')') {
            $lines = $trimmed . ' ';
        }
    }

    protected function indent(&$lines) : void
    {
        $this->indent .= $this->indentStr;
    }

    protected function outdent(&$lines) : void
    {
        $this->indent = substr($this->indent, 0, -1 * strlen($this->indentStr));
    }
}

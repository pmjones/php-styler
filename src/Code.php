<?php
declare(strict_types=1);

namespace PhpStyler;

use ArrayAccess;
use BadMethodCallException;

/**
 * @implements ArrayAccess<int, mixed>
 */
class Code implements ArrayAccess
{
    /**
     * @var mixed[]
     */
    protected array $parts = [];

    protected string $file = '';

    protected string $lines = '';

    protected bool $multiline = false;

    /**
     * @var string[]
     */
    protected array $splitApply = [];

    /**
     * @var array<string, bool>
     */
    protected array $splitCalls = [];

    protected string $indent = '';

    /**
     * @var string[]
     */
    protected array $split = [
        'concat',
        'ternary',
        'array_0',
        'array_1',
        'array_2',
        'array_3',
        'array_4',
        'array_5',
        'function_params',
        'cond',
        'precedence',
        'bool_or',
        'bool_and',
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
        'coalesce',
        'closure_params',
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
        protected string $indentStr,
        protected int $indentLen,
    ) {
        if (! $this->indentLen) {
            $this->indentLen = $this->indentStr === "\t" ? 4 : strlen($indentStr);
        }
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

    public function getFile() : string
    {
        return rtrim($this->file) . $this->eol;
    }

    public function addSplit(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : void
    {
        $split = new Space\Split($class, $level, $type, ...$args);
        $this[] = $split;
        $this->splitCalls[$split->rule] = true;
    }

    public function commit() : void
    {
        $oldIndent = $this->indent;
        $split = $this->split;
        $this->splitApply = [];
        $this->setLines();
        $this->multiline = true;
        $atLeastOneLineTooLong = $this->atLeastOneLineTooLong();

        while ($atLeastOneLineTooLong && $split) {
            $this->indent = $oldIndent;
            $rule = array_shift($split);

            if ($this->splitCalls[$rule] ?? false) {
                $this->splitApply[] = $rule;
                $this->setLines();
                $atLeastOneLineTooLong = $this->atLeastOneLineTooLong();
            }
        }

        $this->multiline = false;

        // retain in file and reset for next round
        $this->file .= $this->lines;
        $this->splitCalls = [];
        $this->parts = [$this->eol . $this->indent];
    }

    protected function atLeastOneLineTooLong() : bool
    {
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

        foreach ($this->parts as $part) {
            if ($part instanceof Space\Space) {
                $method = lcfirst(
                    ltrim((string) strrchr(get_class($part), '\\'), '\\'),
                );
                $this->{$method}($part);
            } else {
                $this->lines .= $part;
            }
        }

        $this->lines = (string) preg_replace("/\\s+\$/m", "\n", $this->lines);
    }

    protected function newline(Space\Newline $newline = null) : void
    {
        $this->lines .= $this->eol . $this->indent;
    }

    protected function clip(Space\Clip $clip = null) : void
    {
        $this->lines = rtrim($this->lines);
    }

    protected function condense(Space\Condense $condense = null) : void
    {
        $trimmed = rtrim($this->lines);

        if ($trimmed === '') {
            $this->file = rtrim($this->file);
        }

        $this->lines = $trimmed . $this->eol . $this->indent;
    }

    // clips the line **only if** the last character is a paren.
    protected function clipToParen(Space\ClipToParen $clipToParen = null) : void
    {
        $trimmed = rtrim($this->lines);
        $lines = explode($this->eol, $trimmed);
        $last = end($lines);

        // can we just use $trimmed instead, and skip the explode()?
        if (trim($last) === ')') {
            $this->lines = $trimmed . ' ';
        }
    }

    protected function indent(Space\Indent $indent = null) : void
    {
        $this->indent .= $this->indentStr;
    }

    protected function outdent(Space\Outdent $outdent = null) : void
    {
        $this->indent = substr($this->indent, 0, -1 * strlen($this->indentStr));
    }

    protected function split(Space\Split $split) : void
    {
        if (! $this->multiline) {
            return;
        }

        if (! in_array($split->rule, $this->splitApply)) {
            return;
        }

        switch ($split->type) {
            case null:
                $this->indent();
                $this->newline();
                break;

            case 'mid':
                $this->lines = rtrim($this->lines);
                $this->newline();
                break;

            case 'end':
                $this->lines .= $split->args[0] ?? '';
                $this->outdent();
                $this->newline();
                break;

            case 'condense':
                $this->indent();
                $this->condense();
                break;

            case 'endCondense':
                $this->outdent();
                $this->condense();
                break;

            case 'outdent':
                $this->outdent();
                break;

            default:
        }
    }
}

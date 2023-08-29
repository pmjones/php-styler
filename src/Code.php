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

    /**
     * @var array<string, bool>
     */
    protected array $force = [];

    protected string $indent = '';

    /**
     * @var string[]
     */
    protected array $split = [
        'args_-1',
        'args_-2',
        'args_-3',
        'args_-4',
        'args_-5',
        'coalesce',
        'ternary',
        'concat',
        'implements',
        'cond',
        'precedence',
        'or',
        'and',
        'array_0',
        'array_1',
        'array_2',
        'array_3',
        'array_4',
        'array_5',
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
    ) : Space\Split
    {
        $split = new Space\Split($class, $level, $type, ...$args);
        $this[] = $split;
        $this->splitCalls[$split->rule] = true;
        return $split;
    }

    public function forceSplit(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : Space\Split
    {
        $split = $this->addSplit($class, $level, $type, ...$args);
        $this->force[$split->rule] = true;
        return $split;
    }

    public function commit() : void
    {
        $oldIndent = $this->indent;
        $split = $this->split;
        $this->splitApply = [];
        $this->setLines();
        $this->multiline = true;

        while ($this->keepSplitting() && $split) {
            $this->indent = $oldIndent;
            $rule = array_shift($split);
            $applySplit = $this->splitCalls[$rule] ?? false;
            $applyForce = $this->force[$rule] ?? false;
            unset($this->force[$rule]);

            if ($applySplit || $applyForce) {
                $this->splitApply[] = $rule;
                $this->setLines();

                // var_dump($this->splitApply);
                // var_dump($this->lines);
            }
        }

        $this->multiline = false;

        // retain in file and reset for next round
        $this->file .= $this->lines;
        $this->splitCalls = [];
        $this->force = [];
        $this->parts = [$this->eol . $this->indent];
    }

    protected function keepSplitting() : bool
    {
        if ($this->force) {
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
                throw new RuntimeException("No such split type: '{$split->type}'");
        }
    }
}

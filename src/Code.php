<?php
declare(strict_types=1);

namespace PhpStyler;

use ArrayAccess;
use BadMethodCallException;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpStyler\Printable as P;

/**
 * @implements ArrayAccess<int, mixed>
 */
class Code implements ArrayAccess
{
    public const SPLIT = [
        P\Args::class => 'args',
        P\AttributeArgs::class => 'attribute_args',
        P\Array_::class => 'array',
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

    protected bool $forceSplit = false;

    protected string $indent = '';

    /**
     * @var string[]
     */
    protected array $split = [];

    /**
     * @param non-empty-string $eol
     * @param string[] $split
     */
    public function __construct(
        protected string $eol,
        protected int $lineLen,
        protected string $indentStr,
        protected int $indentLen,
        array $split,
    ) {
        if (! $this->indentLen) {
            $this->indentLen = $this->indentStr === "\t"
                ? 4
                : strlen($indentStr)
            ;
        }

        $this->setSplitOrder($split);
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

    public function split(
        string $class,
        int $level = null,
        string $type = null,
        mixed ...$args,
    ) : void
    {
        $rule = Code::SPLIT[$class];
        $this[] = ['applySplit', $rule, $level, $type, ...$args];
        $key = $rule . ($level !== null ? "_{$level}" : '');
        $this->splitCalls[$key] = true;
    }

    public function commit() : void
    {
        $oldIndent = $this->indent;
        $split = $this->split;
        $this->splitApply = [];
        $this->setLines();
        $this->multiline = true;

        while ($this->atLeastOneLineTooLong() && $split) {
            $this->indent = $oldIndent;
            $rule = array_shift($split);

            if ($this->splitCalls[$rule] ?? false) {
                $this->splitApply[] = $rule;
                $this->setLines();
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

        foreach ($this->parts as $part) {
            if (is_array($part)) {
                $method = array_shift($part);
                $this->{$method}(...$part);
            } else {
                $this->lines .= $part;
            }
        }

        $this->lines = (string) preg_replace("/\\s+\$/m", "\n", $this->lines);
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

    /**
     * @param string[] $split
     */
    protected function setSplitOrder(array $split) : void
    {
        foreach ($split as $rule) {
            switch ($rule) {
                case 'array':
                    $this->split[] = 'array_0';
                    $this->split[] = 'array_1';
                    $this->split[] = 'array_2';
                    $this->split[] = 'array_3';
                    $this->split[] = 'array_4';
                    $this->split[] = 'array_5';
                    break;

                case 'member_args':
                    $this->split[] = 'member_0';
                    $this->split[] = 'args_0';
                    $this->split[] = 'member_1';
                    $this->split[] = 'args_1';
                    $this->split[] = 'member_2';
                    $this->split[] = 'args_2';
                    $this->split[] = 'member_3';
                    $this->split[] = 'args_3';
                    $this->split[] = 'member_4';
                    $this->split[] = 'args_4';
                    $this->split[] = 'member_5';
                    $this->split[] = 'args_5';
                    break;

                case 'args_member':
                    $this->split[] = 'args_0';
                    $this->split[] = 'member_0';
                    $this->split[] = 'args_1';
                    $this->split[] = 'member_1';
                    $this->split[] = 'args_2';
                    $this->split[] = 'member_2';
                    $this->split[] = 'args_3';
                    $this->split[] = 'member_3';
                    $this->split[] = 'args_4';
                    $this->split[] = 'member_4';
                    $this->split[] = 'args_5';
                    $this->split[] = 'member_5';
                    break;

                case 'args':
                    $this->split[] = 'args_0';
                    $this->split[] = 'args_1';
                    $this->split[] = 'args_2';
                    $this->split[] = 'args_3';
                    $this->split[] = 'args_4';
                    $this->split[] = 'args_5';
                    break;

                case 'attribute_args':
                    $this->split[] = 'attribute_args_0';
                    $this->split[] = 'attribute_args_1';
                    $this->split[] = 'attribute_args_2';
                    $this->split[] = 'attribute_args_3';
                    $this->split[] = 'attribute_args_4';
                    $this->split[] = 'attribute_args_5';
                    break;

                case 'member':
                    $this->split[] = 'member_0';
                    $this->split[] = 'member_1';
                    $this->split[] = 'member_2';
                    $this->split[] = 'member_3';
                    $this->split[] = 'member_4';
                    $this->split[] = 'member_5';
                    break;

                default:
                    $this->split[] = $rule;
                    break;
            }
        }
    }
}

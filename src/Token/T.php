<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

abstract class T extends PhpToken
{
    protected bool $rejoinOrphanBefore = false;

    public int $parenDepth = 0;

    public int $argCount = 0;

    public ?T $openingToken = null;

    public ?T $closingToken = null;

    public bool $transparentOpener = false;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);

        if ($parser->getStyle(static::class)->spaceAfter !== false) {
            $parser->space();
        }
    }

    public function __debugInfo() : array
    {
        $vars = ['CLASS' => get_class($this), 'TOKEN' => $this->getTokenName()];
        $info = $vars + get_object_vars($this);
        $info['openingToken'] = $this->openingToken !== null
            ? get_class($this->openingToken)
            : null;
        $info['closingToken'] = $this->closingToken !== null
            ? get_class($this->closingToken)
            : null;
        return $info;
    }

    public function rejoinOrphanBefore() : bool
    {
        return $this->rejoinOrphanBefore;
    }

    /** @phpstan-assert-if-true T $this->closingToken */
    public function isOpener() : bool
    {
        return ($this->text === '(' || $this->text === '[')
            && $this->closingToken !== null
            && ! $this->transparentOpener;
    }

    public function render(Line $line) : string
    {
        return $this->text;
    }
}

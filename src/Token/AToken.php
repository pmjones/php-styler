<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

abstract class AToken extends PhpToken
{
    public const SYNTHETIC = -1;

    public int $parenDepth = 0;

    public int $argCount = 0;

    public ?AToken $openingToken = null;

    public ?AToken $closingToken = null;

    public function isIgnorable() : bool
    {
        return $this->id === self::SYNTHETIC || parent::isIgnorable();
    }

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

    /**
     * @phpstan-assert-if-true AToken $this->closingToken
     */
    public function isOpener() : bool
    {
        return ($this->text === '(' || $this->text === '[')
            && $this->closingToken !== null;
    }

    public function splitBefore(Parser $parser) : ?TSplit
    {
        return null;
    }

    public function splitAfter(Parser $parser) : ?TSplit
    {
        return null;
    }

    public function render(Line $line) : string
    {
        return $this->text;
    }
}

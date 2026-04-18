<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpStyler\Style;
use PhpToken;

abstract class AToken extends PhpToken
{
    public const SYNTHETIC = -1;

    /** @var ?class-string<self> */
    public const OPENING_BRACE = null;

    /** @var ?class-string<self> */
    public const CLOSING_BRACE = null;

    /** @var ?class-string<self> */
    public const END_SEMICOLON = null;

    public const EXPAND_PRIORITY = null;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);

        if ($parser->getStyle(static::class)->spaceAfter !== false) {
            $parser->space();
        }
    }

    public static function pair(self $opener, self $closer) : void
    {
        $opener->closingToken = $closer;
        $closer->openingToken = $opener;
    }

    /**
     * @param class-string<self> $tokenClass
     */
    public static function new(
        PhpToken $source,
        string $tokenClass,
        Style $style,
        int $parenDepth = 0,
    ) : self
    {
        /** @var self $token */
        $token = new $tokenClass(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );

        $token->style = $style;
        $token->parenDepth = $parenDepth;

        if ($style->case !== null) {
            $token->text = ($style->case)($token->text);
        }

        return $token;
    }

    public int $parenDepth = 0;

    public ?AToken $openingToken = null;

    public ?AToken $closingToken = null;

    public ?Style $style = null;

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

    public function isIgnorable() : bool
    {
        return $this->id === self::SYNTHETIC || parent::isIgnorable();
    }

    public function isContent() : bool
    {
        return true;
    }

    public function expandPriority() : ?int
    {
        return static::EXPAND_PRIORITY;
    }

    public function splObjectId() : int
    {
        return spl_object_id($this);
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

    public function wantsBlankLineAfter() : bool
    {
        return $this->style?->blankLineAfter === true;
    }

    public function render(Line $line) : string
    {
        return $this->text;
    }
}

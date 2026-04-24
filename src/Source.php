<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token;
use PhpStyler\Token\AToken;
use PhpToken;

class Source
{
    private static function hasEol(string $text) : bool
    {
        return str_contains($text, "\r") || str_contains($text, "\n");
    }

    /**
     * @var PhpToken[]
     */
    private array $tokens;

    private int $offset = 0;

    public function __construct(string $code)
    {
        $this->tokens = PhpToken::tokenize($code);
    }

    public function offset() : int
    {
        return $this->offset;
    }

    public function setOffset(int $offset) : void
    {
        $this->offset = $offset;
    }

    public function advance() : void
    {
        $this->offset ++;
    }

    public function hasMore() : bool
    {
        return $this->offset < count($this->tokens);
    }

    public function current() : PhpToken
    {
        return $this->tokens[$this->offset];
    }

    public function count() : int
    {
        return count($this->tokens);
    }

    public function getAt(int $index) : PhpToken
    {
        return $this->tokens[$index];
    }

    public function replaceAt(int $index, PhpToken $token) : void
    {
        $this->tokens[$index] = $token;
    }

    /**
     * @param PhpToken[] $tokens
     */
    public function splice(int $offset, int $deleteCount, array $tokens) : void
    {
        array_splice($this->tokens, $offset, $deleteCount, $tokens);
    }

    public function peek(int $skip = 0) : ?PhpToken
    {
        $i = $this->offset + 1;
        $count = count($this->tokens);

        while ($i < $count) {
            $token = $this->tokens[$i];

            if (! $token->isIgnorable()) {
                if ($skip <= 0) {
                    return $token;
                }

                $skip --;
            }

            $i ++;
        }

        return null;
    }

    public function findNextNonWhitespace(?int $from = null) : ?int
    {
        $i = $from ?? $this->offset + 1;
        $count = count($this->tokens);

        while ($i < $count && $this->tokens[$i]->is(T_WHITESPACE)) {
            $i ++;
        }

        return $i < $count ? $i : null;
    }

    public function findNextNonIgnorable(int $from) : ?int
    {
        $count = count($this->tokens);

        for ($i = $from; $i < $count; $i ++) {
            if (! $this->tokens[$i]->isIgnorable()) {
                return $i;
            }
        }

        // @codeCoverageIgnoreStart
        // defensive: callers look for identifiers following a keyword that
        // is guaranteed by valid PHP to be followed by content
        return null;
        // @codeCoverageIgnoreEnd
    }

    public function matchingCloseParen(int $openOffset) : ?int
    {
        $depth = 1;
        $i = $openOffset + 1;
        $count = count($this->tokens);

        while ($i < $count && $depth > 0) {
            $text = $this->tokens[$i]->text;

            if ($text === '(') {
                $depth ++;
            } elseif ($text === ')') {
                $depth --;
            }

            if ($depth > 0) {
                $i ++;
            }
        }

        return $depth === 0 ? $i : null;
    }

    public function hasPrevEol() : bool
    {
        $prev = $this->tokens[$this->offset - 1] ?? null;

        return $prev !== null
            && $prev->is(T_WHITESPACE)
            && self::hasEol($prev->text);
    }

    public function hasNextEol() : bool
    {
        $token = $this->tokens[$this->offset + 1] ?? null;

        if ($token?->is(T_WHITESPACE)) {
            return self::hasEol($token->text);
        }

        return false;
    }

    /**
     * @param ?callable(int): bool $extraCheck
     */
    public function reclassifyNextIdentifier(?callable $extraCheck = null) : void
    {
        $keywordOffset = $this->findNextNonIgnorable($this->offset + 1);

        // @codeCoverageIgnoreStart
        // defensive: callers only invoke this when an identifier is
        // guaranteed to follow the current token in valid PHP
        if ($keywordOffset === null) {
            return;
        }

        // @codeCoverageIgnoreEnd

        $keyword = $this->tokens[$keywordOffset];

        // only reclassify keyword tokens that look like identifiers,
        // not variables ($foo), braces ({), or other symbols
        if (
            $keyword->id === T_STRING
            || ! preg_match('/^[a-zA-Z_]\w*$/', $keyword->text)
        ) {
            return;
        }

        if ($extraCheck !== null && ! $extraCheck($keywordOffset)) {
            return;
        }

        $this->tokens[$keywordOffset] = new PhpToken(
            T_STRING,
            $keyword->text,
            $keyword->line,
            $keyword->pos,
        );
    }

    public function reclassifyNextAsName() : void
    {
        $this->reclassifyNextIdentifier();
    }

    public function reclassifyNextNamedArg() : void
    {
        $this->reclassifyNextIdentifier(
            function (int $keywordOffset) : bool {
                $next = $this->findNextNonIgnorable($keywordOffset + 1);

                return $next !== null && $this->tokens[$next]->text === ':';
            },
        );
    }

    public function findUpcomingInlineComment() : ?int
    {
        if (self::hasEol($this->tokens[$this->offset]->text)) {
            return null;
        }

        $count = count($this->tokens);

        for ($i = $this->offset + 1; $i < $count; $i ++) {
            $token = $this->tokens[$i];

            if ($token->is(T_WHITESPACE)) {
                if (self::hasEol($token->text)) {
                    return null;
                }

                continue;
            }

            // An AToken here means replaceCommentAt() has already rewritten
            // the source stream in place (converting a raw comment into a
            // *BlankLine/*LineBreak variant); in that case the comment
            // is no longer "upcoming" from this scan's perspective.
            if ($token instanceof AToken) {
                return null;
            }

            return $this->isInlineComment($token, $i) ? $i : null;
        }

        return null;
    }

    private function isInlineComment(PhpToken $token, int $index) : bool
    {
        if (! $token->is(T_COMMENT) && ! $token->is(T_DOC_COMMENT)) {
            return false;
        }

        // // and # always end the line
        if (
            str_starts_with($token->text, '//')
            || str_starts_with($token->text, '#')
        ) {
            return true;
        }

        // /* */ and /** */ — inline only if followed by EOL or EOF
        $next = $this->tokens[$index + 1] ?? null;

        return $next === null
            || ($next->is(T_WHITESPACE) && self::hasEol($next->text));
    }

    public function replaceCommentAt(int $index, bool $blankLine) : void
    {
        $token = $this->tokens[$index];

        $class = match (true) {
            $token->is(T_DOC_COMMENT) => $blankLine
                ? Token\TDocCommentBlankLine::class
                : Token\TDocCommentLineBreak::class,

            str_starts_with($token->text, '//') => $blankLine
                ? Token\TCommentSlashedBlankLine::class
                : Token\TCommentSlashedLineBreak::class,

            str_starts_with($token->text, '#') => $blankLine
                ? Token\TCommentHashedBlankLine::class
                : Token\TCommentHashedLineBreak::class,

            default => $blankLine
                ? Token\TCommentStarredBlankLine::class
                : Token\TCommentStarredLineBreak::class,
        };

        $this->tokens[$index] = new $class(
            $token->id,
            $token->text,
            $token->line,
            $token->pos,
        );
    }
}

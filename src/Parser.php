<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\Format;
use PhpStyler\Format\PlainFormat;
use PhpStyler\Token;
use PhpStyler\Token\AToken;
use PhpToken;

class Parser
{
    private Format $format;

    public const TOKEN_CLASS = [
        '$' => Token\TDollar::class,
        '"' => Token\TDoubleQuote::class,
        '(' => Token\TOpeningParen::class,
        ')' => Token\TClosingParen::class,
        ',' => Token\TComma::class,
        '.' => Token\TDot::class,
        ':' => Token\TColon::class,
        ';' => Token\TSemicolon::class,
        '<' => Token\TSmallerThan::class,
        '=' => Token\TAssign::class,
        '>' => Token\TGreaterThan::class,
        '?' => Token\TQuestion::class,
        '[' => Token\TOpeningBracket::class,
        ']' => Token\TClosingBracket::class,
        '{' => Token\TOpeningBrace::class,
        '|' => Token\TPipe::class,
        '}' => Token\TClosingBrace::class,
        '+' => Token\TPlus::class, // addition, unary plus
        '-' => Token\TMinus::class, // subtraction, unary minus
        '*' => Token\TMultiply::class,
        '/' => Token\TDivide::class,
        '%' => Token\TModulo::class,
        '!' => Token\TNot::class,
        '~' => Token\TTilde::class,
        '^' => Token\TCaret::class,
        '@' => Token\TAt::class,
        '`' => Token\TBacktick::class,
        'T_CLASS_C' => Token\TMagicClassConstant::class,
        'T_FUNC_C' => Token\TMagicFunctionConstant::class,
        'T_METHOD_C' => Token\TMagicMethodConstant::class,
        'T_TRAIT_C' => Token\TMagicTraitConstant::class,
        'T_NS_C' => Token\TMagicNamespaceConstant::class,
        'T_DIR' => Token\TMagicDirConstant::class,
        'T_FILE' => Token\TMagicFileConstant::class,
        'T_LINE' => Token\TMagicLineConstant::class,
        'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG' => Token\TReference::class,
        'T_PAAMAYIM_NEKUDOTAYIM' => Token\TDoubleColon::class,
        'T_DNUMBER' => Token\TFloatLiteral::class,
        'T_LNUMBER' => Token\TIntegerLiteral::class,
        'T_SL' => Token\TShiftLeft::class,
        'T_SL_EQUAL' => Token\TShiftLeftEqual::class,
        'T_SR' => Token\TShiftRight::class,
        'T_SR_EQUAL' => Token\TShiftRightEqual::class,
        'T_INC' => Token\TIncrement::class,
        'T_DEC' => Token\TDecrement::class,
        'T_MUL_EQUAL' => Token\TMultiplyEqual::class,
        'T_DIV_EQUAL' => Token\TDivideEqual::class,
        'T_MOD_EQUAL' => Token\TModuloEqual::class,
        'T_POW' => Token\TPower::class,
        'T_POW_EQUAL' => Token\TPowerEqual::class,
        'T_PROPERTY_C' => Token\TMagicPropertyConstant::class,
        'T_CONSTANT_ENCAPSED_STRING' => Token\TStringLiteral::class,
        'T_ENCAPSED_AND_WHITESPACE' => Token\TStringFragment::class,
        'T_NUM_STRING' => Token\TStringNumericIndex::class,
        'T_NAME_FULLY_QUALIFIED' => Token\TFullyQualifiedName::class,
        'T_NAME_QUALIFIED' => Token\TQualifiedName::class,
        'T_NAME_RELATIVE' => Token\TRelativeName::class,
        'T_OPEN_TAG' => Token\TPhpOpeningTag::class,
        'T_CLOSE_TAG' => Token\TPhpClosingTag::class,
        'T_OPEN_TAG_WITH_ECHO' => Token\TPhpEchoOpeningTag::class,
        'T_START_HEREDOC' => Token\THeredocStart::class,
        'T_END_HEREDOC' => Token\THeredocEnd::class,
        'T_NS_SEPARATOR' => Token\TNamespaceSeparator::class,
    ];

    /**
     * @var array<int, Nesting>
     */
    protected array $nesting = [];

    /**
     * @var array<int, AToken>
     */
    protected array $parsed = [];

    protected int $parsedCount = 0;

    /**
     * @var array<PhpToken>
     */
    protected array $source = [];

    protected int $sourceCount = 0;

    protected int $sourceOffset = 0;

    protected int $lastAddedIndex = 0;

    protected int $parenDepth = 0;

    public ?Token\TSplit $lastSplit = null;

    /** @var array<class-string<AToken>, Style> */
    private array $styles = [];

    public function __construct(?Format $format = null)
    {
        $this->format = $format ?? new PlainFormat();
    }

    /**
     * @param class-string<AToken> $class
     */
    public function getStyle(string $class) : Style
    {
        if (isset($this->styles[$class])) {
            return $this->styles[$class];
        }

        $args = $this->format->styles[$class] ?? [];
        $style = new Style(...$args);
        $this->styles[$class] = $style;
        return $style;
    }

    /**
     * @return array<int, AToken>
     */
    public function __invoke(string $code) : array
    {
        $this->nesting = [];
        $this->parsed = [];
        $this->parsedCount = 0;
        $this->lastAddedIndex = 0;
        $this->source = PhpToken::tokenize($code);
        $this->sourceCount = count($this->source);
        $this->sourceOffset = 0;
        $this->parenDepth = 0;
        $this->lastSplit = null;

        for (
            $this->sourceOffset = 0;
            $this
                ->sourceOffset < $this
                ->sourceCount;
                $this
                ->sourceOffset ++
        ) {
            $source = $this->source[$this->sourceOffset];

            /** @var class-string<AToken> $tokenClass */
            $tokenClass = $this->getTokenClass($source);
            $this->parse($source, $tokenClass);
        }

        $this->removePrevWhitespace();

        return $this->parsed;
    }

    protected function getTokenClass(PhpToken $source) : string
    {
        if ($source instanceof AToken) {
            return get_class($source);
        }

        $name = (string) $source->getTokenName();

        if (str_starts_with($name, 'T_')) {
            return self::TOKEN_CLASS[$name]
                ?? 'PhpStyler\\Token\\'
                    . str_replace('_', '', ucwords(strtolower($name), '_'));
        }

        return self::TOKEN_CLASS[$source->text];
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function parse(PhpToken $source, string $tokenClass) : void
    {
        /** @var AToken $tokenClass */
        $tokenClass = $this->format->parses[$tokenClass] ?? $tokenClass;
        $tokenClass::parse($this, $source);
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function add(PhpToken $source, string $tokenClass) : AToken
    {
        $this->removePrevWhitespace();
        $style = $this->getStyle($tokenClass);

        if ($style->spaceBefore === true) {
            $this->space();
        } elseif ($style->spaceBefore === false) {
            $this->noSpace();
        }

        if ($style->blankLineBefore === true) {
            $this->blankLine();
        } elseif ($style->lineBreakBefore === true) {
            $this->lineBreak();
        }

        /** @var AToken $token */
        $token = new $tokenClass(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );

        if ($style->case !== null) {
            $token->text = ($style->case)($token->text);
        }

        if ($token->text === ')' || $token->text === ']') {
            $this->parenDepth = max(0, $this->parenDepth - 1);
        }

        $token->parenDepth = $this->parenDepth;

        if ($token->text === '(' || $token->text === '[') {
            $this->parenDepth ++;
        }

        if ($token instanceof Token\TSplittableComma && $this->nesting !== []) {
            $this->nesting[array_key_last($this->nesting)]->argCount ++;
        }

        $splitBefore = $token->splitBefore($this);

        if ($splitBefore !== null) {
            $this->addSplit($splitBefore);
        }

        $this->lastAddedIndex = $this->parsedCount;
        $this->emit($token);

        $splitAfter = $token->splitAfter($this);

        if ($splitAfter !== null) {
            $this->addSplit($splitAfter);
        }

        if ($style->spaceAfter === true) {
            $this->space();
        }

        if ($style->blankLineAfter === true) {
            $this->blankLine();
        } elseif ($style->lineBreakAfter === true) {
            $this->lineBreak();
        }

        return $token;
    }

    public function addSplit(Token\TSplit $split) : void
    {
        $this->emit($split);
        $this->lastSplit = $split;
    }

    public function replaceLastSplit(Token\TSplit $replacement) : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            if ($this->parsed[$i] === $this->lastSplit) {
                $this->parsed[$i] = $replacement;
                $this->lastSplit = $replacement;
                return;
            }
        }
    }

    public function indentIncr() : void
    {
        $this->emit(new Token\TIndentIncrement(AToken::SYNTHETIC, ''));
    }

    public function indentDecr() : void
    {
        if (
            $this->parsedCount > 0
            && $this->parsed[
                $this->parsedCount - 1
            ] instanceof Token\TIndentIncrement
        ) {
            $this->removeParsedAt($this->parsedCount - 1);
            return;
        }

        $this->emit(new Token\TIndentDecrement(AToken::SYNTHETIC, ''));
    }

    public function blankLine() : void
    {
        if ($this->hasPrevBlankLine()) {
            return;
        }

        if ($this->hasPrevOpeningStructure()) {
            return;
        }

        $this->removeTrailingSpaces();

        if (
            ! (
                $this->parsedCount > 0
                && $this->parsed[$this->parsedCount - 1] instanceof Token\TLineBreak
            )
        ) {
            $commentIndex = $this->findUpcomingInlineComment();

            if ($commentIndex !== null) {
                $this->replaceSourceComment($commentIndex, blankLine: true);
                return;
            }
        }

        $this->lineBreak();
        $this->emit(new Token\TBlankLine(T_WHITESPACE, "\n\n"));
        $this->lineBreak();
    }

    public function removeTrailingBlankLine() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            if ($this->parsed[$i] instanceof Token\TBlankLine) {
                $this->removeParsedAt($i);
                return;
            }

            if (! $this->parsed[$i]->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                return;
            }
        }
    }

    public function lineBreak() : void
    {
        $this->removeTrailingSpaces();

        if (
            $this->parsedCount > 0
            && $this->parsed[$this->parsedCount - 1] instanceof Token\TLineBreak
        ) {
            return;
        }

        $commentIndex = $this->findUpcomingInlineComment();

        if ($commentIndex !== null) {
            $this->replaceSourceComment($commentIndex, blankLine: false);
            return;
        }

        $this->emit(new Token\TLineBreak(T_WHITESPACE, "\n"));
    }

    private function removeTrailingSpaces() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TSpace) {
                $this->removeParsedAt($i);
            } elseif (! $prev->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                break;
            }
        }
    }

    public function space() : void
    {
        if ($this->parsedCount === 0) {
            return;
        }

        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TSpace) {
                return;
            }

            if ($prev instanceof Token\TLineBreak) {
                return;
            }

            if (! $prev->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                break;
            }
        }

        $this->emit(new Token\TSpace(T_WHITESPACE, ' '));
    }

    private function emit(AToken $token) : void
    {
        $this->parsed[] = $token;
        $this->parsedCount ++;
    }

    private function removeParsedAt(int $index) : void
    {
        array_splice($this->parsed, $index, 1);
        $this->parsedCount --;
    }

    private function noSpace() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TSpace) {
                $this->removeParsedAt($i);
                return;
            }

            if ($prev instanceof Token\TLineBreak) {
                return;
            }

            if (! $prev->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                return;
            }
        }
    }

    private function removePrevWhitespace() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if (
                $prev instanceof Token\TWhitespaceEol
                || $prev instanceof Token\TWhitespace
            ) {
                $this->removeParsedAt($i);
                continue;
            }

            if (! $prev->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                return;
            }
        }
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function replaceLastParsed(PhpToken $source, string $tokenClass) : AToken
    {
        $this->removePrevWhitespace();

        /** @var AToken $token */
        $token = new $tokenClass(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );

        $this->parsed[$this->lastAddedIndex] = $token;
        return $token;
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function addNesting(PhpToken $source, string $tokenClass) : AToken
    {
        $token = $this->add($source, $tokenClass);
        $this->nesting[] = new Nesting($token);
        return $token;
    }

    /**
     * @param class-string $kind
     */
    public function atNesting(string $kind, string ...$kinds) : bool
    {
        array_unshift($kinds, $kind);
        $nestingOffset = count($this->nesting);

        foreach ($kinds as $kind) {
            $nestingOffset --;
            $nesting = $this->nesting[$nestingOffset] ?? null;

            if (! $nesting?->token instanceof $kind) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return class-string
     */
    public function getNesting() : string
    {
        $nesting = end($this->nesting);

        /** @var class-string */
        return $nesting !== false ? get_class($nesting->token) : '';
    }

    /**
     * @return array<int, class-string>
     */
    public function listNesting() : array
    {
        return array_map(fn (Nesting $n) => get_class($n->token), $this->nesting);
    }

    public function inEncapsedString() : bool
    {
        return $this->atNesting(Token\TCurlyOpen::class)
            || $this->atNesting(Token\TDollarOpenCurlyBraces::class)
            || $this->atNesting(Token\TDoubleQuoteOpening::class)
            || $this->atNesting(Token\THeredocStart::class)
            || $this->atNesting(Token\TBacktickOpening::class);
    }

    public function endBracelessBody(PhpToken $source) : void
    {
        $isContinuation = $this->getNextSource()?->is([T_ELSE, T_ELSEIF]);
        $this->popNesting(Token\TOpeningBraceless::class);
        $nesting = $this->getNesting();

        $braceless = match (true) {
            $isContinuation
                && $nesting === Token\TIf::class => Token\TIfContinuationBraceless::class,
            $isContinuation
                && $nesting === Token\TElseif::class => Token\TElseifContinuationBraceless::class,
            ! $isContinuation
                && $nesting === Token\TIf::class => Token\TIfClosingBraceless::class,
            ! $isContinuation
                && $nesting === Token\TElse::class => Token\TElseClosingBraceless::class,
            ! $isContinuation
                && $nesting === Token\TElseif::class => Token\TElseifClosingBraceless::class,
            ! $isContinuation
                && $nesting === Token\TWhile::class => Token\TWhileClosingBraceless::class,
            ! $isContinuation
                && $nesting === Token\TFor::class => Token\TForClosingBraceless::class,
            ! $isContinuation
                && $nesting === Token\TForeach::class => Token\TForeachClosingBraceless::class,
            default => throw new Exception(
                ($isContinuation ? "Unknown continuation" : "Unknown closing")
                    . " braceless in nesting "
                    . var_export($this->listNesting(), true),
            ),
        };

        $this->parse($source, $braceless);
    }

    public function popTernaryNesting() : void
    {
        while (true) {
            $nesting = end($this->nesting);

            if ($nesting === false) {
                break;
            }

            $token = $nesting->token;

            if (
                $token instanceof Token\TTernaryColon
                || $token instanceof Token\TElvisColon
                || $token instanceof Token\TTernaryQuestion
                || $token instanceof Token\TElvisQuestion
            ) {
                array_pop($this->nesting);
            } elseif ($token instanceof Token\TFnDoubleArrow) {
                array_pop($this->nesting); // TFnDoubleArrow
                array_pop($this->nesting); // TFn
            } else {
                break;
            }
        }
    }

    /**
     * @param class-string<AToken> $closerClass
     */
    public function closeNesting(
        PhpToken $source,
        string $closerClass,
        string $openerClass,
        string ...$openerClasses,
    ) : AToken
    {
        $current = end($this->nesting);
        $argCount = $current !== false ? $current->argCount : 0;
        $opener = $this->popNesting($openerClass, ...$openerClasses);
        $opener->argCount = $argCount;
        $closer = $this->add($source, $closerClass);
        $opener->closingToken = $closer;
        $closer->openingToken = $opener;

        return $closer;
    }

    public function popNesting(string $expect, string ...$expects) : AToken
    {
        $expects = [$expect, ...$expects];
        $nesting = array_pop($this->nesting);
        $actual = $nesting?->token;
        $actualClass = $actual !== null ? get_class($actual) : '';

        if (! in_array($actualClass, $expects)) {
            throw new \RuntimeException(
                "Expected to pop "
                    . implode('|', $expects)
                    . ", got {$actualClass} instead",
            );
        }

        /** @var AToken $actual */
        return $actual;
    }

    public function getPrevParsed(int $skip = 0) : ?AToken
    {
        $found = 0;

        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            if (! $this->parsed[$i]->isIgnorable()) {
                if ($found >= $skip) {
                    return $this->parsed[$i];
                }

                $found ++;
            }
        }

        return null;
    }

    public function getNextSource() : ?PhpToken
    {
        $sourceOffset = $this->sourceOffset + 1;

        while ($sourceOffset < $this->sourceCount) {
            $source = $this->source[$sourceOffset];

            if (! $source->isIgnorable()) {
                return $source;
            }

            $sourceOffset ++;
        }

        return null;
    }

    public function findNextNonWhitespaceOffset(?int $from = null) : ?int
    {
        $i = $from ?? $this->sourceOffset + 1;

        while ($i < $this->sourceCount && $this->source[$i]->is(T_WHITESPACE)) {
            $i ++;
        }

        return $i < $this->sourceCount ? $i : null;
    }

    public function findMatchingCloseParenOffset(int $openOffset) : ?int
    {
        $depth = 1;
        $i = $openOffset + 1;

        while ($i < $this->sourceCount && $depth > 0) {
            $text = $this->source[$i]->text;

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

    public function hasPrevLineBreak() : bool
    {
        return $this->hasPrev(Token\TLineBreak::class)
            || $this->hasPrev(Token\TWhitespaceEol::class);
    }

    public function hasPrevSplittableComma() : bool
    {
        return $this->hasPrev(Token\TSplittableComma::class);
    }

    public function hasPrevEol() : bool
    {
        return $this->hasPrev(Token\TWhitespaceEol::class);
    }

    public function hasPrevBlankLine() : bool
    {
        return $this->hasPrev(Token\TBlankLine::class);
    }

    private function hasPrevOpeningStructure() : bool
    {
        return $this->hasPrev(Token\TOpeningStructure::class);
    }

    private function hasPrev(string $class) : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $parsed = $this->parsed[$i];

            if ($parsed instanceof $class) {
                return true;
            } elseif (! $parsed->is([AToken::SYNTHETIC, T_WHITESPACE])) {
                return false;
            }
        }

        return false;
    }

    public function hasNextEol() : bool
    {
        $source = $this->source[$this->sourceOffset + 1] ?? null;

        if ($source?->is(T_WHITESPACE)) {
            return self::hasEol($source->text);
        }

        return false;
    }

    private static function hasEol(string $text) : bool
    {
        return strpos($text, "\r") !== false || strpos($text, "\n") !== false;
    }

    protected function findUpcomingInlineComment() : ?int
    {
        $currentText = $this->source[$this->sourceOffset]->text;

        if (self::hasEol($currentText)) {
            return null;
        }

        for ($i = $this->sourceOffset + 1; $i < $this->sourceCount; $i ++) {
            $source = $this->source[$i];

            if ($source->is(T_WHITESPACE)) {
                if (self::hasEol($source->text)) {
                    return null; // newline before comment
                }

                continue;
            }

            // Skip already-replaced tokens
            if ($source instanceof AToken) {
                return null;
            }

            if (! $source->is(T_COMMENT) && ! $source->is(T_DOC_COMMENT)) {
                return null; // non-comment token
            }

            // // and # always end the line
            if (
                str_starts_with($source->text, '//')
                || str_starts_with($source->text, '#')
            ) {
                return $i;
            }

            // /* */ and /** */ — must have EOL after to be end-of-line
            $next = $this->source[$i + 1] ?? null;

            if ($next === null) {
                return $i; // end of file
            }

            if ($next->is(T_WHITESPACE) && self::hasEol($next->text)) {
                return $i;
            }

            return null; // code continues after comment
        }

        return null;
    }

    public function getSourceOffset() : int
    {
        return $this->sourceOffset;
    }

    public function getSourceAt(int $index) : PhpToken
    {
        return $this->source[$index];
    }

    public function setSourceAt(int $index, PhpToken $token) : void
    {
        $this->source[$index] = $token;
    }

    public function getSourceCount() : int
    {
        return $this->sourceCount;
    }

    public function setSourceOffset(int $offset) : void
    {
        $this->sourceOffset = $offset;
    }

    /**
     * @param PhpToken[] $tokens
     */
    public function spliceSource(
        int $offset,
        int $deleteCount,
        array $tokens,
    ) : void
    {
        array_splice($this->source, $offset, $deleteCount, $tokens);
        $this->sourceCount = count($this->source);
    }

    protected function replaceSourceComment(int $index, bool $blankLine) : void
    {
        $source = $this->source[$index];

        $class = match (true) {
            $source->is(T_DOC_COMMENT) => $blankLine
                ? Token\TDocCommentBlankLine::class
                : Token\TDocCommentLineBreak::class,
            str_starts_with($source->text, '//') => $blankLine
                ? Token\TCommentSlashedBlankLine::class
                : Token\TCommentSlashedLineBreak::class,
            str_starts_with($source->text, '#') => $blankLine
                ? Token\TCommentHashedBlankLine::class
                : Token\TCommentHashedLineBreak::class,
            default => $blankLine
                ? Token\TCommentStarredBlankLine::class
                : Token\TCommentStarredLineBreak::class,
        };

        $this->source[$index] = new $class(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );
    }
}

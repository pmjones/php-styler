<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Style\Style;
use PhpStyler\Style\StyleLocator;
use PhpStyler\Token;
use PhpStyler\Token\T;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TExtendsComma;
use PhpStyler\Token\TImplementsComma;
use PhpStyler\Token\TSplitPoint;
use PhpStyler\Token\TSplittable;
use PhpStyler\Token\TSplittableComma;
use PhpToken;

class Parser
{
    private StyleLocator $styles;

    public const PARSE_CLASS = [
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
     * @var array<int, T>
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

    public ?TSplitPoint $lastSplitPoint = null;

    public function __construct(?StyleLocator $styles = null)
    {
        $this->styles = $styles ?? new StyleLocator();
    }

    /**
     * @param class-string<T> $class
     */
    public function getStyle(string $class) : Style
    {
        return $this->styles->get($class);
    }

    /**
     * @return array<int, T>
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
        $this->lastSplitPoint = null;

        for ($this->sourceOffset = 0; $this->sourceOffset < $this->sourceCount; $this->sourceOffset ++) {
            $source = $this->source[$this->sourceOffset];
            /** @var class-string<T> $parseClass */
            $parseClass = $this->getParseClass($source);
            $this->parse($source, $parseClass);
        }

        $this->removePrevWhitespace();

        return $this->parsed;
    }

    protected function getParseClass(PhpToken $source) : string
    {
        if ($source instanceof T) {
            return get_class($source);
        }

        $name = (string) $source->getTokenName();

        if (str_starts_with($name, 'T_')) {
            return self::PARSE_CLASS[$name]
                ?? "\\PhpStyler\\Token\\"
                    . str_replace('_', '', ucwords(strtolower($name), '_'));
        }

        return self::PARSE_CLASS[$source->text];
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function parse(PhpToken $source, string $parseClass) : void
    {
        $parseClass::parse($this, $source);
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function add(PhpToken $source, string $parseClass) : T
    {
        $this->removePrevWhitespace();
        $style = $this->getStyle($parseClass);

        if ($style->spaceBefore === false) {
            $this->noSpace();
        }

        if ($style->blankLineBefore === true) {
            $this->blankLine();
        } elseif ($style->lineBreakBefore === true) {
            $this->lineBreak();
        }

        /** @var T $token */
        $token = new $parseClass(
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

        if ($token instanceof TSplittableComma && $this->nesting !== []) {
            $this->nesting[array_key_last($this->nesting)]->argCount ++;
        }

        // Emit TSplitPoint BEFORE operators/fluent
        if ($token instanceof TSplittable && ! $token instanceof TSplittableComma) {
            $this->addSplitPoint($token->splitCategory());
        }

        $this->lastAddedIndex = $this->parsedCount;
        $this->emit($token);

        // Emit TSplitPoint AFTER commas
        if ($token instanceof TSplittableComma) {
            $isListComma = $token instanceof TExtendsComma
                || $token instanceof TImplementsComma;
            $this->addSplitPoint($token->splitCategory(), continuation: $isListComma);
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

    public function addSplitPoint(int $priority, bool $continuation = true) : void
    {
        $splitPoint = new TSplitPoint(T_WHITESPACE, '');
        $splitPoint->splitPriority = $priority;
        $splitPoint->continuation = $continuation;
        $this->emit($splitPoint);
        $this->lastSplitPoint = $splitPoint;
    }

    public function indentIncr() : void
    {
        $this->emit(new TIndentIncrement(T_WHITESPACE, ''));
    }

    public function indentDecr() : void
    {
        if (
            $this->parsedCount > 0
            && $this->parsed[$this->parsedCount - 1] instanceof TIndentIncrement
        ) {
            array_pop($this->parsed);
            $this->parsedCount --;
            return;
        }

        $this->emit(new TIndentDecrement(T_WHITESPACE, ''));
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

        if (! $this->hasPrevLineBreakToken()) {
            $commentIndex = $this->findUpcomingInlineComment();

            if ($commentIndex !== null) {
                $this->replaceSourceComment($commentIndex, blankLine: true);
                return;
            }
        }

        $this->lineBreak();
        $this->emit(new Token\TBlankLine(T_WHITESPACE, ''));
        $this->lineBreak();
    }

    public function removeTrailingBlankLine() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            if ($this->parsed[$i] instanceof Token\TBlankLine) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount --;
                return;
            }

            if (! $this->parsed[$i]->is(T_WHITESPACE)) {
                return;
            }
        }
    }

    public function lineBreak() : void
    {
        $this->removeTrailingSpaces();

        if ($this->hasPrevLineBreakToken()) {
            return;
        }

        $commentIndex = $this->findUpcomingInlineComment();

        if ($commentIndex !== null) {
            $this->replaceSourceComment($commentIndex, blankLine: false);
            return;
        }

        $this->emit(new TLineBreak(T_WHITESPACE, ''));
    }

    private function removeTrailingSpaces() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof TSpace) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount --;
            } elseif (! $prev->is(T_WHITESPACE)) {
                break;
            }
        }
    }

    private function hasPrevLineBreakToken() : bool
    {
        return $this->parsedCount > 0
            && $this->parsed[$this->parsedCount - 1] instanceof TLineBreak;
    }

    public function space() : void
    {
        if ($this->parsedCount === 0) {
            return;
        }

        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof TSpace) {
                return;
            }

            if ($prev instanceof TLineBreak) {
                return;
            }

            if (! $prev->is(T_WHITESPACE)) {
                break;
            }
        }

        $this->emit(new TSpace(T_WHITESPACE, ' '));
    }

    private function emit(T $token) : void
    {
        $this->parsed[] = $token;
        $this->parsedCount ++;
    }

    public function noSpace() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof TSpace) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount --;
                return;
            }

            if ($prev instanceof TLineBreak) {
                return;
            }

            if (! $prev->is(T_WHITESPACE)) {
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
                array_splice($this->parsed, $i, 1);
                $this->parsedCount --;
                continue;
            }

            if (! $prev->is(T_WHITESPACE)) {
                return;
            }
        }
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function replaceLastParsed(PhpToken $source, string $parseClass) : T
    {
        $this->removePrevWhitespace();

        /** @var T $token */
        $token = new $parseClass(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );

        $this->parsed[$this->lastAddedIndex] = $token;
        return $token;
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function addNesting(PhpToken $source, string $parseClass) : T
    {
        $token = $this->add($source, $parseClass);
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
        return array_map(fn(Nesting $n) => get_class($n->token), $this->nesting);
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
     * @param class-string<T> $closerClass
     */
    public function closeNesting(
        PhpToken $source,
        string $closerClass,
        string $openerClass,
        string ...$openerClasses,
    ) : T
    {
        $current = end($this->nesting);
        $argCount = $current !== false ? $current->argCount : 0;
        $containsBracket = $current !== false ? $current->containsBracket : false;
        $opener = $this->popNesting($openerClass, ...$openerClasses);
        $opener->argCount = $argCount;
        $closer = $this->add($source, $closerClass);
        $opener->closingToken = $closer;
        $closer->openingToken = $opener;

        if ($opener->text === '[' && $this->nesting !== []) {
            $this->nesting[array_key_last($this->nesting)]->containsBracket = true;
        } elseif (
            $argCount === 0
            && $opener->text === '('
            && $containsBracket
            && ! $this->containsSplittableOperator($opener)
        ) {
            $opener->transparentOpener = true;
        }

        return $closer;
    }

    public function popNesting(string $expect, string ...$expects) : T
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

        /** @var T $actual */
        return $actual;
    }

    public function getPrevParsed(int $skip = 0) : ?T
    {
        $before = null;

        for ($i = 0; $i <= $skip; $i ++) {
            $before = $this->getPrevTokenOffset($before);

            if ($before === null) {
                return null;
            }
        }

        /** @var int $before */
        return $this->parsed[$before];
    }

    protected function getPrevTokenOffset(?int $before = null) : ?int
    {
        $parsedOffset = ($before ?? $this->parsedCount) - 1;

        while ($parsedOffset >= 0) {
            $parsed = $this->parsed[$parsedOffset];

            if (! $parsed->isIgnorable()) {
                return $parsedOffset;
            }

            $parsedOffset --;
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

    public function hasPrevLineBreak() : bool
    {
        return $this->hasPrev(Token\TLineBreak::class)
            || $this->hasPrev(Token\TWhitespaceEol::class);
    }

    public function hasPrevEol() : bool
    {
        return $this->hasPrev(Token\TWhitespaceEol::class);
    }

    public function hasPrevBlankLine() : bool
    {
        return $this->hasPrev(Token\TBlankLine::class);
    }

    public function hasPrevOpeningStructure() : bool
    {
        return $this->hasPrev(Token\TOpeningStructure::class);
    }

    private function hasPrev(string $class) : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $parsed = $this->parsed[$i];

            if ($parsed instanceof $class) {
                return true;
            } elseif (! $parsed->is(T_WHITESPACE)) {
                return false;
            }
        }

        return false;
    }

    public function hasNextEol() : bool
    {
        $source = $this->source[$this->sourceOffset + 1] ?? null;

        if ($source?->is(T_WHITESPACE)) {
            return strpos($source->text, "\r") !== false
                || strpos($source->text, "\n") !== false;
        }

        return false;
    }

    private function containsSplittableOperator(T $opener) : bool
    {
        $depth = 0;

        for ($i = $this->lastAddedIndex - 1; $i >= 0; $i --) {
            $token = $this->parsed[$i];

            if ($token === $opener) {
                return false;
            }

            if ($token->text === ')' || $token->text === ']') {
                $depth ++;
            } elseif ($token->text === '(' || $token->text === '[') {
                $depth --;
            } elseif ($depth === 0 && $token instanceof Token\TSplittableOperator) {
                return true;
            }
        }

        return false;
    }

    protected function findUpcomingInlineComment() : ?int
    {
        $currentText = $this->source[$this->sourceOffset]->text;

        if (strpos($currentText, "\r") !== false || strpos($currentText, "\n") !== false) {
            return null;
        }

        for ($i = $this->sourceOffset + 1; $i < $this->sourceCount; $i ++) {
            $source = $this->source[$i];

            if ($source->is(T_WHITESPACE)) {
                if (strpos($source->text, "\r") !== false
                    || strpos($source->text, "\n") !== false
                ) {
                    return null; // newline before comment
                }

                continue;
            }

            // Skip already-replaced tokens
            if ($source instanceof T) {
                return null;
            }

            if (! $source->is(T_COMMENT) && ! $source->is(T_DOC_COMMENT)) {
                return null; // non-comment token
            }

            // // and # always end the line
            if (str_starts_with($source->text, '//') || str_starts_with($source->text, '#')) {
                return $i;
            }

            // /* */ and /** */ — must have EOL after to be end-of-line
            $next = $this->source[$i + 1] ?? null;

            if ($next === null) {
                return $i; // end of file
            }

            if ($next->is(T_WHITESPACE)
                && (strpos($next->text, "\r") !== false
                    || strpos($next->text, "\n") !== false)
            ) {
                return $i;
            }

            return null; // code continues after comment
        }

        return null;
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

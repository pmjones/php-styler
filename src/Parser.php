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
     * @var array<int, T>
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
    protected array $unparsed = [];

    protected int $unparsedCount = 0;

    protected int $unparsedOffset = 0;

    protected int $lastAddedIndex = 0;

    protected int $parenDepth = 0;

    /** @var int[] Comma count per nesting level, parallel to $nesting */
    protected array $nestingArgCount = [];

    /** @var bool[] Whether each nesting level directly contains a bracket child */
    protected array $nestingContainsBracket = [];

    public ?TSplitPoint $lastSplitPoint = null;

    public function __construct(
        ?StyleLocator $styles = null,
    ) {
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
        $this->unparsed = PhpToken::tokenize($code);
        $this->unparsedCount = count($this->unparsed);
        $this->unparsedOffset = 0;
        $this->parenDepth = 0;
        $this->nestingArgCount = [];
        $this->nestingContainsBracket = [];
        $this->lastSplitPoint = null;

        foreach ($this->unparsed as $this->unparsedOffset => $unparsed) {
            /** @var class-string<T> $parseClass */
            $parseClass = $this->getParseClass($unparsed);
            $this->parse($unparsed, $parseClass);
        }

        $this->removePrevWhitespace();

        return $this->parsed;
    }

    protected function getParseClass(PhpToken $unparsed) : string
    {
        $name = (string) $unparsed->getTokenName();

        if (str_starts_with($name, 'T_')) {
            return self::PARSE_CLASS[$name]
                ?? "\\PhpStyler\\Token\\" . str_replace('_', '', ucwords(strtolower($name), '_'));
        }

        return self::PARSE_CLASS[$unparsed->text];
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function parse(PhpToken $unparsed, string $parseClass) : void
    {
        $parseClass::parse($this, $unparsed);
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function add(PhpToken $unparsed, string $parseClass) : T
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
            $unparsed->id,
            $unparsed->text,
            $unparsed->line,
            $unparsed->pos,
        );

        if ($style->case !== null) {
            $token->text = ($style->case)($token->text);
        }

        if ($token->text === ')' || $token->text === ']') {
            $this->parenDepth = max(0, $this->parenDepth - 1);
        }

        $token->parenDepth = $this->parenDepth;

        if ($token->text === '(' || $token->text === '[') {
            $this->parenDepth++;
        }

        if ($token instanceof TSplittableComma && $this->nestingArgCount !== []) {
            $this->nestingArgCount[array_key_last($this->nestingArgCount)]++;
        }

        // Emit TSplitPoint BEFORE operators/fluent
        if ($token instanceof TSplittable && ! $token instanceof TSplittableComma) {
            $this->addSplitPoint($token->splitCategory());
        }

        $this->parsed[] = $token;
        $this->lastAddedIndex = $this->parsedCount;
        $this->parsedCount++;

        // Emit TSplitPoint AFTER commas
        if ($token instanceof TSplittableComma) {
            $isListComma = $token instanceof TExtendsComma || $token instanceof TImplementsComma;
            $this->addSplitPoint(
                $token->splitCategory(),
                continuation: $isListComma,
            );
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
        $this->parsed[] = $splitPoint;
        $this->parsedCount++;
        $this->lastSplitPoint = $splitPoint;
    }

    public function indentIncr() : void
    {
        $this->parsed[] = new TIndentIncrement(T_WHITESPACE, '');
        $this->parsedCount++;
    }

    public function indentDecr() : void
    {
        if ($this->parsedCount > 0
            && $this->parsed[$this->parsedCount - 1] instanceof TIndentIncrement
        ) {
            array_pop($this->parsed);
            $this->parsedCount--;
            return;
        }

        $this->parsed[] = new TIndentDecrement(T_WHITESPACE, '');
        $this->parsedCount++;
    }

    public function blankLine() : void
    {
        if ($this->hasPrevBlankLine()) {
            return;
        }

        if ($this->hasPrevOpeningStructure()) {
            return;
        }

        $this->lineBreak();
        $this->parsed[] = new Token\TBlankLine(T_WHITESPACE, '');
        $this->parsedCount++;
        $this->lineBreak();
    }

    public function removeTrailingBlankLine() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
            if ($this->parsed[$i] instanceof Token\TBlankLine) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount--;
                return;
            }

            if (! $this->parsed[$i]->is(T_WHITESPACE)) {
                return;
            }
        }
    }

    public function lineBreak() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
            $prev = $this->parsed[$i];

            if ($prev instanceof TSpace) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount--;
            } elseif (! $prev->is(T_WHITESPACE)) {
                break;
            }
        }

        if ($this->parsedCount > 0 && $this->parsed[$this->parsedCount - 1] instanceof TLineBreak) {
            return;
        }

        $this->parsed[] = new TLineBreak(T_WHITESPACE, '');
        $this->parsedCount++;
    }

    public function space() : void
    {
        if ($this->parsedCount === 0) {
            return;
        }

        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
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

        $this->parsed[] = new TSpace(T_WHITESPACE, ' ');
        $this->parsedCount++;
    }

    public function noSpace() : void
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
            $prev = $this->parsed[$i];

            if ($prev instanceof TSpace) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount--;
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
        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TWhitespaceEol || $prev instanceof Token\TWhitespace) {
                array_splice($this->parsed, $i, 1);
                $this->parsedCount--;
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
    public function replaceLastParsed(PhpToken $unparsed, string $parseClass) : T
    {
        $this->removePrevWhitespace();
        /** @var T $token */
        $token = new $parseClass(
            $unparsed->id,
            $unparsed->text,
            $unparsed->line,
            $unparsed->pos,
        );

        $this->parsed[$this->lastAddedIndex] = $token;
        return $token;
    }

    /**
     * @param class-string<T> $parseClass
     */
    public function addNesting(PhpToken $unparsed, string $parseClass) : T
    {
        $token = $this->add($unparsed, $parseClass);
        $this->nesting[] = $token;
        $this->nestingArgCount[] = 0;
        $this->nestingContainsBracket[] = false;
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

            if (! $nesting instanceof $kind) {
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
        $token = end($this->nesting);
        /** @var class-string */
        return $token !== false ? get_class($token) : '';
    }

    /**
     * @return array<int, class-string>
     */
    public function listNesting() : array
    {
        return array_map(get_class(...), $this->nesting);
    }

    public function popTernaryNesting() : void
    {
        while (true) {
            $token = end($this->nesting);

            if ($token === false) {
                break;
            }

            if (
                $token instanceof Token\TTernaryColon
                || $token instanceof Token\TElvisColon
                || $token instanceof Token\TTernaryQuestion
                || $token instanceof Token\TElvisQuestion
            ) {
                array_pop($this->nesting);
                array_pop($this->nestingArgCount);
                array_pop($this->nestingContainsBracket);
            } elseif ($token instanceof Token\TFnDoubleArrow) {
                array_pop($this->nesting); // TFnDoubleArrow
                array_pop($this->nestingArgCount);
                array_pop($this->nestingContainsBracket);
                array_pop($this->nesting); // TFn
                array_pop($this->nestingArgCount);
                array_pop($this->nestingContainsBracket);
            } else {
                break;
            }
        }
    }

    /**
     * @param class-string<T> $closerClass
     */
    public function closeNesting(PhpToken $unparsed, string $closerClass, string $openerClass, string ...$openerClasses) : T
    {
        $argCount = array_pop($this->nestingArgCount) ?? 0;
        $containsBracket = array_pop($this->nestingContainsBracket) ?? false;
        $opener = $this->popNesting($openerClass, ...$openerClasses);
        $opener->argCount = $argCount;
        $closer = $this->add($unparsed, $closerClass);
        $opener->closingToken = $closer;
        $closer->openingToken = $opener;

        if ($opener->text === '[' && $this->nestingContainsBracket !== []) {
            $this->nestingContainsBracket[array_key_last($this->nestingContainsBracket)] = true;
        } elseif ($argCount === 0 && $opener->text === '(' && $containsBracket) {
            $opener->transparentOpener = true;
        }

        return $closer;
    }

    public function popNesting(string $expect, string ...$expects) : T
    {
        $expects = [$expect, ...$expects];
        $actual = array_pop($this->nesting);

        // Pop arg count if not already popped by closeNesting
        if (count($this->nestingArgCount) > count($this->nesting)) {
            array_pop($this->nestingArgCount);
        }

        if (count($this->nestingContainsBracket) > count($this->nesting)) {
            array_pop($this->nestingContainsBracket);
        }

        $actualClass = $actual !== null ? get_class($actual) : '';

        if (! in_array($actualClass, $expects)) {
            throw new \RuntimeException(
                "Expected to pop " . implode('|', $expects) . ", got {$actualClass} instead",
            );
        }

        /** @var T $actual */
        return $actual;
    }

    public function getPrevParsed(int $skip = 0) : ?T
    {
        $before = null;

        for ($i = 0; $i <= $skip; $i++) {
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

    public function getNextUnparsed() : ?PhpToken
    {
        $unparsedOffset = $this->unparsedOffset + 1;

        while ($unparsedOffset < $this->unparsedCount) {
            $unparsed = $this->unparsed[$unparsedOffset];

            if (! $unparsed->isIgnorable()) {
                return $unparsed;
            }

            $unparsedOffset ++;
        }

        return null;
    }

    public function transferLineBreakAfter(T $to) : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i--) {
            if ($this->parsed[$i] === $to) {
                continue;
            }

            if ($this->parsed[$i] instanceof TLineBreak) {
                array_splice($this->parsed, $i, 1, [new TSpace(T_WHITESPACE, ' ')]);

                // clean up adjacent TBlankLine and its preceding TLineBreak
                while ($i > 0 && $this->parsed[$i - 1] instanceof Token\TBlankLine) {
                    array_splice($this->parsed, $i - 1, 1);
                    $this->parsedCount--;
                    $i--;
                }

                if ($i > 0 && $this->parsed[$i - 1] instanceof TLineBreak) {
                    array_splice($this->parsed, $i - 1, 1);
                    $this->parsedCount--;
                    $i--;
                }

                $this->lineBreak();
                return true;
            }

            if (! $this->parsed[$i]->is(T_WHITESPACE)) {
                return false;
            }
        }

        return false;
    }

    public function hasPrevSourceNewline() : bool
    {
        $unparsed = $this->unparsed[$this->unparsedOffset - 1] ?? null;

        if ($unparsed === null) {
            return false;
        }

        if ($unparsed->is(T_WHITESPACE)) {
            return strpos($unparsed->text, "\r") !== false
                || strpos($unparsed->text, "\n") !== false;
        }

        return str_ends_with($unparsed->text, "\r")
            || str_ends_with($unparsed->text, "\n");
    }

    public function hasPrevEol() : bool
    {
        $parsedOffset = $this->parsedCount - 1;

        while ($parsedOffset >= 0) {
            $parsed = $this->parsed[$parsedOffset];

            if ($parsed instanceof Token\TWhitespaceEol) {
                return true;
            } elseif (! $parsed->is(T_WHITESPACE)) {
                return false;
            }

            $parsedOffset--;
        }

        return false;
    }

    public function hasPrevBlankLine() : bool
    {
        $parsedOffset = $this->parsedCount - 1;

        while ($parsedOffset >= 0) {
            $parsed = $this->parsed[$parsedOffset];

            if ($parsed instanceof Token\TBlankLine) {
                return true;
            } elseif (! $parsed->is(T_WHITESPACE)) {
                return false;
            }

            $parsedOffset--;
        }

        return false;
    }

    public function hasPrevOpeningStructure() : bool
    {
        $parsedOffset = $this->parsedCount - 1;

        while ($parsedOffset >= 0) {
            $parsed = $this->parsed[$parsedOffset];

            if ($parsed instanceof Token\TOpeningStructure) {
                return true;
            } elseif (! $parsed->is(T_WHITESPACE)) {
                return false;
            }

            $parsedOffset--;
        }

        return false;
    }

    public function hasNextEol() : bool
    {
        $unparsed = $this->unparsed[$this->unparsedOffset + 1] ?? null;

        if ($unparsed?->is(T_WHITESPACE)) {
            return strpos($unparsed->text, "\r") !== false
                || strpos($unparsed->text, "\n") !== false;
        }

        return false;
    }
}

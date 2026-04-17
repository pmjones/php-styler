<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\AFormat;
use PhpStyler\Format\PlainFormat;
use PhpStyler\Token;
use PhpStyler\Token\AToken;
use PhpToken;

class Parser
{
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
     * @var array<int, int>
     */
    private const MODIFIER_PRIORITY = [
        T_ABSTRACT => 1,
        T_FINAL => 1,
        T_PUBLIC => 2,
        T_PROTECTED => 2,
        T_PRIVATE => 2,
        T_VAR => 2,
        T_PUBLIC_SET => 3,
        T_PROTECTED_SET => 3,
        T_PRIVATE_SET => 3,
        T_STATIC => 4,
        T_READONLY => 5,
    ];

    /** @var array<string, class-string<AToken>> */
    private const BRACELESS_CONTINUATION = [
        Token\TIf::class => Token\TIfContinuationBraceless::class,
        Token\TElseif::class => Token\TElseifContinuationBraceless::class,
    ];

    /** @var array<string, class-string<AToken>> */
    private const BRACELESS_CLOSING = [
        Token\TIf::class => Token\TIfClosingBraceless::class,
        Token\TElse::class => Token\TElseClosingBraceless::class,
        Token\TElseif::class => Token\TElseifClosingBraceless::class,
        Token\TWhile::class => Token\TWhileClosingBraceless::class,
        Token\TFor::class => Token\TForClosingBraceless::class,
        Token\TForeach::class => Token\TForeachClosingBraceless::class,
    ];

    private static function hasEol(string $text) : bool
    {
        return str_contains($text, "\r") || str_contains($text, "\n");
    }

    private AFormat $format;

    private NestingStack $nestingStack;

    /**
     * @var array<int, AToken>
     */
    private array $parsed = [];

    private int $parsedCount = 0;

    /**
     * @var array<PhpToken>
     */
    private array $source = [];

    private int $sourceCount = 0;

    private int $sourceOffset = 0;

    private int $parenDepth = 0;

    public ?Token\TSplit $lastSplit = null;

    private int $lastSplitIndex = -1;

    private int $fluentChainIndex = -1;

    private int $fluentChainPosition = -1;

    /** @var array<string, string> */
    private array $tokenClass = [];

    /** @var array<class-string<AToken>, Style> */
    private array $styles = [];

    public function __construct(?AFormat $format = null)
    {
        $this->format = $format ?? new PlainFormat();
    }

    /**
     * @return array<int, AToken>
     */
    public function __invoke(string $code) : array
    {
        $this->nestingStack = new NestingStack();
        $this->parsed = [];
        $this->parsedCount = 0;
        $this->source = PhpToken::tokenize($code);
        $this->sourceCount = count($this->source);
        $this->sourceOffset = 0;
        $this->parenDepth = 0;
        $this->lastSplit = null;
        $this->lastSplitIndex = -1;
        $this->fluentChainIndex = -1;
        $this->fluentChainPosition = -1;

        for (
            $this->sourceOffset = 0;
            $this->sourceOffset < $this->sourceCount;
            $this->sourceOffset ++
        ) {
            $source = $this->source[$this->sourceOffset];

            // Skip non-EOL whitespace — it produces no parsed output
            if (
                $source->id === T_WHITESPACE
                && strpbrk($source->text, "\r\n") === false
            ) {
                continue;
            }

            /** @var class-string<AToken> $tokenClass */
            $tokenClass = $this->getTokenClass($source);
            $this->parse($source, $tokenClass);
        }

        $this->removePrevWhitespace();

        return $this->parsed;
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

    private function getTokenClass(PhpToken $source) : string
    {
        if ($source instanceof AToken) {
            return get_class($source);
        }

        $name = (string) $source->getTokenName();

        if (str_starts_with($name, 'T_')) {
            $this->tokenClass[$name] ??= 'PhpStyler\\Token\\'
                . str_replace('_', '', ucwords(strtolower($name), '_'));

            return self::TOKEN_CLASS[$name] ?? $this->tokenClass[$name];
        }

        return self::TOKEN_CLASS[$source->text];
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function parse(PhpToken $source, string $tokenClass) : void
    {
        /** @var AToken $tokenClass */
        $tokenClass = $this->format->parseAs[$tokenClass] ?? $tokenClass;
        $tokenClass::parse($this, $source);
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function add(PhpToken $source, string $tokenClass) : AToken
    {
        $style = $this->getStyle($tokenClass);
        $this->applyStyleBefore($style);
        $token = $this->createToken($source, $tokenClass, $style);

        $splitBefore = $token->splitBefore($this);

        if ($splitBefore !== null) {
            $this->addSplit($splitBefore);
        }

        $this->emit($token);

        $splitAfter = $token->splitAfter($this);

        if ($splitAfter !== null) {
            $this->addSplit($splitAfter);
        }

        $this->applyStyleAfter($style);

        return $token;
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    private function createToken(
        PhpToken $source,
        string $tokenClass,
        Style $style,
    ) : AToken
    {
        if ($source->text === ')' || $source->text === ']') {
            $this->parenDepth = max(0, $this->parenDepth - 1);
        }

        $token = AToken::new($source, $tokenClass, $style, $this->parenDepth);

        if ($source->text === '(' || $source->text === '[') {
            $this->parenDepth ++;
        }

        if ($token instanceof Token\AMemberClosing) {
            $token->closesStaticMember = $this->hasPrevStatic();
        }

        if ($token instanceof Token\ASplittableComma) {
            $this->nestingStack->incrementArgCount();
        }

        return $token;
    }

    private function applyStyleBefore(Style $style) : void
    {
        $this->removePrevWhitespace();

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
    }

    private function applyStyleAfter(Style $style) : void
    {
        if ($style->spaceAfter === true) {
            $this->space();
        }

        if ($style->blankLineAfter === true) {
            $this->blankLine();
        } elseif ($style->lineBreakAfter === true) {
            $this->lineBreak();
        }
    }

    public function addSplit(Token\TSplit $split) : void
    {
        $this->lastSplitIndex = $this->parsedCount;
        $this->emit($split);
        $this->lastSplit = $split;
    }

    /**
     * @return array{int, int}
     */
    public function startFluentChain() : array
    {
        $this->fluentChainIndex ++;
        $this->fluentChainPosition = 0;

        return [$this->fluentChainIndex, $this->fluentChainPosition];
    }

    /**
     * @return array{int, int}
     */
    public function continueFluentChain() : array
    {
        $this->fluentChainPosition ++;

        return [$this->fluentChainIndex, $this->fluentChainPosition];
    }

    public function replaceLastSplit(Token\TSplit $replacement) : void
    {
        if (
            $this->lastSplitIndex >= 0
            && $this->lastSplitIndex < $this->parsedCount
            && $this->parsed[$this->lastSplitIndex] === $this->lastSplit
        ) {
            if (
                $this->lastSplit instanceof Token\TSplitFluent
                && $replacement instanceof Token\TSplitFluent
            ) {
                $replacement->chainIndex = $this->lastSplit->chainIndex;
                $replacement->chainPosition = $this->lastSplit->chainPosition;
            }

            $this->parsed[$this->lastSplitIndex] = $replacement;
            $this->lastSplit = $replacement;
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
        if ($this->hasPrev(Token\TBlankLine::class)) {
            return;
        }

        if ($this->hasPrev(Token\AnOpeningStructure::class)) {
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
        $this->scanParsedTrail(
            function (AToken $token, int $i) : ?bool {
                if ($token instanceof Token\TBlankLine) {
                    $this->removeParsedAt($i);
                    return true;
                }

                return $token->is([AToken::SYNTHETIC, T_WHITESPACE]) ? null : false;
            },
        );
    }

    public function removeTrailingLineBreaks() : void
    {
        $this->scanParsedTrail(
            function (AToken $token, int $i) : ?bool {
                if (
                    $token instanceof Token\TLineBreak
                    || $token instanceof Token\TBlankLine
                ) {
                    $this->removeParsedAt($i);

                    return null;
                }

                return $token->is([AToken::SYNTHETIC, T_WHITESPACE]) ? null : false;
            },
        );
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
        $this->scanParsedTrail(
            function (AToken $token, int $i) : ?bool {
                if ($token instanceof Token\TSpace) {
                    $this->removeParsedAt($i);
                    return null;
                }

                return $token->is([AToken::SYNTHETIC, T_WHITESPACE]) ? null : false;
            },
        );
    }

    public function space() : void
    {
        if ($this->parsedCount === 0) {
            return;
        }

        $found = $this->scanParsedTrail(
            fn (AToken $token) : ?bool => match (true) {
                $token instanceof Token\TSpace,
                $token instanceof Token\TLineBreak => true,

                $token->is([AToken::SYNTHETIC, T_WHITESPACE]) => null,
                default => false,
            },
        );

        if (! $found) {
            $this->emit(new Token\TSpace(T_WHITESPACE, ' '));
        }
    }

    /**
     * Scan backwards through parsed tokens.
     *
     * Callback receives (AToken $token, int $index) and returns:
     *
     * - null: skip this token, keep scanning
     * - true: stop scanning (found/handled)
     * - false: stop scanning (not found/not handled)
     *
     * @param callable(AToken, int):?bool $callback
     */
    private function scanParsedTrail(callable $callback) : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $result = $callback($this->parsed[$i], $i);

            if ($result !== null) {
                return $result;
            }
        }

        return false;
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
        $this->scanParsedTrail(
            function (AToken $token, int $i) : ?bool {
                if ($token instanceof Token\TSpace) {
                    $this->removeParsedAt($i);
                    return true;
                }

                return match (true) {
                    $token instanceof Token\TLineBreak => true,
                    $token->is([AToken::SYNTHETIC, T_WHITESPACE]) => null,
                    default => false,
                };
            },
        );
    }

    private function removePrevWhitespace() : void
    {
        $this->scanParsedTrail(
            function (AToken $token, int $i) : ?bool {
                if (
                    $token instanceof Token\TWhitespaceEol
                    || $token instanceof Token\TWhitespace
                ) {
                    $this->removeParsedAt($i);
                    return null;
                }

                return $token->is([AToken::SYNTHETIC, T_WHITESPACE]) ? null : false;
            },
        );
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function addNesting(PhpToken $source, string $tokenClass) : AToken
    {
        $token = $this->add($source, $tokenClass);
        $this->nestingStack->push($token);
        return $token;
    }

    /**
     * @param class-string<AToken> $tokenClass
     */
    public function pushNesting(PhpToken $source, string $tokenClass) : void
    {
        /** @var AToken */
        $token = new $tokenClass(
            $source->id,
            $source->text,
            $source->line,
            $source->pos,
        );

        $this->nestingStack->push($token);
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getNestingOpeningBrace() : ?string
    {
        return $this->nestingStack->getOpeningBrace();
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getNestingClosingBrace() : ?string
    {
        return $this->nestingStack->getClosingBrace();
    }

    /**
     * @return ?class-string<AToken>
     */
    public function getNestingEndSemicolon() : ?string
    {
        return $this->nestingStack->getEndSemicolon();
    }

    /**
     * @param class-string $kind
     */
    public function atNesting(string $kind, string ...$kinds) : bool
    {
        return $this->nestingStack->at($kind, ...$kinds);
    }

    /**
     * @return class-string
     */
    public function getNesting() : string
    {
        return $this->nestingStack->getClass();
    }

    /**
     * @return array<int, class-string>
     */
    public function listNesting() : array
    {
        return $this->nestingStack->listAll();
    }

    public function inEncapsedString() : bool
    {
        return $this->nestingStack->inEncapsedString();
    }

    public function endBracelessBody(PhpToken $source) : void
    {
        $isContinuation = $this->getNextSource()?->is([T_ELSE, T_ELSEIF]);
        $this->popNesting(Token\TOpeningBraceless::class);
        $nesting = $this->getNesting();

        $map = $isContinuation
            ? self::BRACELESS_CONTINUATION
            : self::BRACELESS_CLOSING;

        $braceless = $map[$nesting]
            ?? throw Exception::fromParser(
                ($isContinuation ? "Unknown continuation" : "Unknown closing")
                    . " braceless on line {$source->line}"
                    . " at position {$source->pos}",
                $this,
                $source,
            );

        $this->parse($source, $braceless);
    }

    public function popTernaryNesting() : void
    {
        $this->nestingStack->popTernary();
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
        $argCount = $this->nestingStack->getArgCount();
        $opener = $this->popNesting($openerClass, ...$openerClasses);
        $opener->argCount = $argCount;
        $closer = $this->add($source, $closerClass);
        AToken::pair($opener, $closer);

        if ($closer instanceof Token\AMemberClosing) {
            $closer->closesStaticMember = $this->hasPrevStaticBefore($opener);
        }

        return $closer;
    }

    public function popNesting(string $expect, string ...$expects) : AToken
    {
        $token = $this->nestingStack->pop();
        $expects = [$expect, ...$expects];

        if ($token === null || ! in_array(get_class($token), $expects, true)) {
            $actual = $token !== null ? get_class($token) : '';

            throw Exception::fromParser(
                "Expected to pop "
                    . implode('|', $expects)
                    . ", got {$actual} instead",
                $this,
                $this->source[$this->sourceOffset],
            );
        }

        return $token;
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

    public function getParsedCount() : int
    {
        return $this->parsedCount;
    }

    public function getParsedAt(int $index) : AToken
    {
        return $this->parsed[$index];
    }

    public function swapParsedAt(int $a, int $b) : void
    {
        [
            $this->parsed[$a],
            $this->parsed[$b],
        ] = [$this->parsed[$b], $this->parsed[$a]];
    }

    public function getNextSource(int $skip = 0) : ?PhpToken
    {
        $sourceOffset = $this->sourceOffset + 1;

        while ($sourceOffset < $this->sourceCount) {
            $source = $this->source[$sourceOffset];

            if (! $source->isIgnorable()) {
                if ($skip <= 0) {
                    return $source;
                }

                $skip --;
            }

            $sourceOffset ++;
        }

        return null;
    }

    public function reclassifyNextSourceAsName() : void
    {
        $offset = $this->sourceOffset + 1;

        while ($offset < $this->sourceCount) {
            $source = $this->source[$offset];

            if ($source->isIgnorable()) {
                $offset ++;
                continue;
            }

            // only reclassify keyword tokens that look like identifiers,
            // not variables ($foo), braces ({), or other symbols
            if (
                $source->id !== T_STRING
                && preg_match('/^[a-zA-Z_]\w*$/', $source->text)
            ) {
                $this->source[$offset] = new \PhpToken(
                    T_STRING,
                    $source->text,
                    $source->line,
                    $source->pos,
                );
            }

            return;
        }
    }

    public function reclassifyNextNamedArg() : void
    {
        $keywordOffset = null;

        for ($i = $this->sourceOffset + 1; $i < $this->sourceCount; $i ++) {
            if (! $this->source[$i]->isIgnorable()) {
                $keywordOffset = $i;
                break;
            }
        }

        if ($keywordOffset === null) {
            return;
        }

        $keyword = $this->source[$keywordOffset];

        if (
            $keyword->id === T_STRING
            || ! preg_match('/^[a-zA-Z_]\w*$/', $keyword->text)
        ) {
            return;
        }

        // reclassify only if the token after the keyword is ':'
        for ($j = $keywordOffset + 1; $j < $this->sourceCount; $j ++) {
            if (! $this->source[$j]->isIgnorable()) {
                if ($this->source[$j]->text === ':') {
                    $this->source[$keywordOffset] = new \PhpToken(
                        T_STRING,
                        $keyword->text,
                        $keyword->line,
                        $keyword->pos,
                    );
                }

                return;
            }
        }
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

    public function hasPrev(string $class) : bool
    {
        return $this->scanParsedTrail(
            fn (AToken $token) : ?bool => match (true) {
                $token instanceof $class => true,
                $token->is([AToken::SYNTHETIC, T_WHITESPACE]) => null,
                default => false,
            },
        );
    }

    public function hasPrevSourceEol() : bool
    {
        $prev = $this->source[$this->sourceOffset - 1] ?? null;

        return $prev !== null
            && $prev->is(T_WHITESPACE)
            && self::hasEol($prev->text);
    }

    public function hasNextEol() : bool
    {
        $source = $this->source[$this->sourceOffset + 1] ?? null;

        if ($source?->is(T_WHITESPACE)) {
            return self::hasEol($source->text);
        }

        return false;
    }

    private function findUpcomingInlineComment() : ?int
    {
        if (self::hasEol($this->source[$this->sourceOffset]->text)) {
            return null;
        }

        for ($i = $this->sourceOffset + 1; $i < $this->sourceCount; $i ++) {
            $source = $this->source[$i];

            if ($source->is(T_WHITESPACE)) {
                if (self::hasEol($source->text)) {
                    return null;
                }

                continue;
            }

            if ($source instanceof AToken) {
                return null;
            }

            return $this->isInlineComment($source, $i) ? $i : null;
        }

        return null;
    }

    private function isInlineComment(PhpToken $source, int $index) : bool
    {
        if (! $source->is(T_COMMENT) && ! $source->is(T_DOC_COMMENT)) {
            return false;
        }

        // // and # always end the line
        if (
            str_starts_with($source->text, '//')
            || str_starts_with($source->text, '#')
        ) {
            return true;
        }

        // /* */ and /** */ — inline only if followed by EOL or EOF
        $next = $this->source[$index + 1] ?? null;

        return $next === null
            || ($next->is(T_WHITESPACE) && self::hasEol($next->text));
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

    private function replaceSourceComment(int $index, bool $blankLine) : void
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

    public function atClassBody() : bool
    {
        return $this->atNesting(Token\TClasslikeOpeningBrace::class)
            || $this->atNesting(
                Token\TAnonymousOpeningBrace::class,
                Token\TAnonymousClass::class,
            );
    }

    public function isPrevStaticPropertyAccess() : bool
    {
        for ($i = $this->parsedCount - 2; $i >= 0; $i --) {
            $token = $this->parsed[$i];

            if ($token->isContent()) {
                return $token instanceof Token\TMemberDoubleColon;
            }
        }

        return false;
    }

    public function hasPrevStatic() : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TStatic) {
                return true;
            }

            // stop at class body boundaries or previous member endings
            if (
                $prev instanceof Token\AMemberClosing
                || $prev instanceof Token\TClasslikeOpeningBrace
                || $prev instanceof Token\TAnonymousOpeningBrace
            ) {
                return false;
            }
        }

        return false;
    }

    private function hasPrevStaticBefore(Token\AToken $before) : bool
    {
        $found = false;

        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            if (! $found) {
                if ($this->parsed[$i] === $before) {
                    $found = true;
                }

                continue;
            }

            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TStatic) {
                return true;
            }

            if (
                $prev instanceof Token\AMemberClosing
                || $prev instanceof Token\TClasslikeOpeningBrace
                || $prev instanceof Token\TAnonymousOpeningBrace
            ) {
                return false;
            }
        }

        return false;
    }

    public function hasPrevVisibility() : bool
    {
        for ($i = $this->parsedCount - 1; $i >= 0; $i --) {
            $prev = $this->parsed[$i];

            if ($prev instanceof Token\TSpace) {
                continue;
            }

            if ($prev instanceof Token\AModifier) {
                if (
                    $prev instanceof Token\TPublic
                    || $prev instanceof Token\TProtected
                    || $prev instanceof Token\TPrivate
                ) {
                    return true;
                }

                continue;
            }

            return false;
        }

        return false;
    }

    public function handleModifier() : void
    {
        $modifiers = [];
        $i = $this->sourceOffset;

        while ($i < $this->sourceCount) {
            $source = $this->source[$i];

            if ($source->is(T_WHITESPACE)) {
                $i ++;
                continue;
            }

            if (! isset(self::MODIFIER_PRIORITY[$source->id])) {
                break;
            }

            $modifiers[] = $source;
            $i ++;
        }

        foreach ($modifiers as $idx => $mod) {
            if ($idx > 0) {
                $this->space();
            }

            /** @var class-string<AToken> $tokenClass */
            $tokenClass = $this->getTokenClass($mod);
            $this->add($mod, $tokenClass);
        }

        $this->space();
        $this->sourceOffset = $i - 1;
    }
}

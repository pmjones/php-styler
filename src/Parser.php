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

    private AFormat $format;

    private NestingStack $nestingStack;

    /**
     * @var array<int, AToken>
     */
    private array $parsed = [];

    public Source $source;

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
        $this->source = new Source($code);
        $this->lastSplit = null;
        $this->lastSplitIndex = -1;
        $this->fluentChainIndex = -1;
        $this->fluentChainPosition = -1;

        for (
            $this->source->setOffset(0);
            $this->source->hasMore();
            $this->source->advance()
        ) {
            $source = $this->source->current();

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
        $token = AToken::new($source, $tokenClass, $style);

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
        $this->lastSplitIndex = count($this->parsed);
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
            && $this->lastSplitIndex < count($this->parsed)
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
            count($this->parsed) > 0
            && $this->parsed[count($this->parsed) - 1]
                instanceof Token\TIndentIncrement
        ) {
            $this->removeParsedAt(count($this->parsed) - 1);
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
                count($this->parsed) > 0
                && $this->parsed[count($this->parsed) - 1]
                    instanceof Token\TLineBreak
            )
        ) {
            $commentIndex = $this->source->findUpcomingInlineComment();

            if ($commentIndex !== null) {
                $this->source->replaceCommentAt($commentIndex, blankLine: true);
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
                if ($token instanceof Token\ALineBreaking) {
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
            count($this->parsed) > 0
            && $this->parsed[count($this->parsed) - 1] instanceof Token\TLineBreak
        ) {
            return;
        }

        $commentIndex = $this->source->findUpcomingInlineComment();

        if ($commentIndex !== null) {
            $this->source->replaceCommentAt($commentIndex, blankLine: false);
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
        if (count($this->parsed) === 0) {
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
     * Scan backwards through parsed tokens — trail-walk semantics.
     *
     * Use this for whitespace-cleanup scans where you want to inspect the trail
     * of recently-emitted tokens but STOP when you encounter real content
     * (including comments). Whitespace and synthetic tokens are skippable; the
     * callback decides via its return value.
     *
     * For content-walks (find the previous PHP token, treating whitespace AND
     * comments AND splits as transparent), use `prevContentTokens()` instead.
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
        for ($i = count($this->parsed) - 1; $i >= 0; $i --) {
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
    }

    private function removeParsedAt(int $index) : void
    {
        array_splice($this->parsed, $index, 1);
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
        $isContinuation = $this->source->peek()?->is([T_ELSE, T_ELSEIF]);
        $this->popNesting(Token\TOpeningBraceless::class);

        $braceless = $isContinuation
            ? $this->nestingStack->getContinuationBraceless()
            : $this->nestingStack->getClosingBraceless();

        if ($braceless === null) {
            throw Exception::fromParser(
                ($isContinuation ? "Unknown continuation" : "Unknown closing")
                    . " braceless on line {$source->line}"
                    . " at position {$source->pos}",
                $this,
                $source,
            );
        }

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

        if ($opener instanceof Token\ACommaListOpener) {
            $opener->argCount = $argCount;
        }

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
                $this->source->current(),
            );
        }

        return $token;
    }

    public function getPrevParsed(int $skip = 0) : ?AToken
    {
        foreach ($this->prevContentTokens() as $token) {
            if ($skip -- <= 0) {
                return $token;
            }
        }

        return null;
    }

    /**
     * Yield previous content tokens, newest → oldest — content-walk semantics.
     *
     * Use this when you want to look back at the actual previous PHP token(s),
     * treating whitespace, splits, comments, and other ignorables as
     * transparent. The yielded keys are the original parsed-array indices.
     *
     * For trail-walk scans where comments STOP the search (e.g. blank-line
     * placement decisions), use `scanParsedTrail()` instead.
     *
     * @return \Generator<int, AToken>
     */
    private function prevContentTokens(?int $fromIndex = null) : \Generator
    {
        $i = $fromIndex ?? count($this->parsed) - 1;

        for (; $i >= 0; $i --) {
            $token = $this->parsed[$i];

            if (! $token->isIgnorable()) {
                yield $i => $token;
            }
        }
    }

    public function getParsedCount() : int
    {
        return count($this->parsed);
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

    public function hasPrevLineBreak() : bool
    {
        return $this->hasPrev(Token\ALineBreaking::class);
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
        // skip the most recent parsed token (the just-emitted prev) and look one
        // further back — the caller is splitBefore() on a member operator, asking
        // "is the property name immediately preceded by ::?"
        foreach ($this->prevContentTokens(count($this->parsed) - 2) as $token) {
            return $token instanceof Token\TMemberDoubleColon;
        }

        return false;
    }

    private function hasPrevStatic() : bool
    {
        return $this->hasPrevStaticFrom(count($this->parsed) - 1);
    }

    private function hasPrevStaticBefore(Token\AToken $before) : bool
    {
        foreach ($this->prevContentTokens() as $i => $token) {
            if ($token === $before) {
                return $this->hasPrevStaticFrom($i - 1);
            }
        }

        return false;
    }

    private function hasPrevStaticFrom(int $fromIndex) : bool
    {
        foreach ($this->prevContentTokens($fromIndex) as $token) {
            if ($token instanceof Token\TStatic) {
                return true;
            }

            // stop at class body boundaries or previous member endings
            if (
                $token instanceof Token\AMemberClosing
                || $token instanceof Token\TClasslikeOpeningBrace
                || $token instanceof Token\TAnonymousOpeningBrace
            ) {
                return false;
            }
        }

        return false;
    }

    public function handleModifier() : void
    {
        $modifiers = [];
        $i = $this->source->offset();
        $count = $this->source->count();

        while ($i < $count) {
            $source = $this->source->getAt($i);

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
        $this->source->setOffset($i - 1);
    }
}

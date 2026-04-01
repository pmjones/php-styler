<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\TAssign;
use PhpStyler\Token\TBinaryPlus;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TBooleanOr;
use PhpStyler\Token\TBreak;
use PhpStyler\Token\TCase;
use PhpStyler\Token\TCaseColon;
use PhpStyler\Token\TCatch;
use PhpStyler\Token\TCatchContinuationBrace;
use PhpStyler\Token\TCatchOpeningBrace;
use PhpStyler\Token\TClass;
use PhpStyler\Token\TClassClosingBrace;
use PhpStyler\Token\TClassName;
use PhpStyler\Token\TClassOpeningBrace;
use PhpStyler\Token\TCommentHashed;
use PhpStyler\Token\TCommentHashedLineBreak;
use PhpStyler\Token\TCommentSlashed;
use PhpStyler\Token\TCommentSlashedLineBreak;
use PhpStyler\Token\TCommentSlashedMidStatement;
use PhpStyler\Token\TCommentStarred;
use PhpStyler\Token\TCommentStarredLineBreak;
use PhpStyler\Token\TCommentStarredMidStatement;
use PhpStyler\Token\TDefaultAfterCase;
use PhpStyler\Token\TDefaultMatch;
use PhpStyler\Token\TDo;
use PhpStyler\Token\TDocComment;
use PhpStyler\Token\TDoContinuationBrace;
use PhpStyler\Token\TDoOpeningBrace;
use PhpStyler\Token\TDoWhileEndSemicolon;
use PhpStyler\Token\TEcho;
use PhpStyler\Token\TEchoEndSemicolon;
use PhpStyler\Token\TEndif;
use PhpStyler\Token\TFinally;
use PhpStyler\Token\TFinallyClosingBrace;
use PhpStyler\Token\TFinallyOpeningBrace;
use PhpStyler\Token\TFor;
use PhpStyler\Token\TForClosingBrace;
use PhpStyler\Token\TForClosingParen;
use PhpStyler\Token\TForComma;
use PhpStyler\Token\TForeach;
use PhpStyler\Token\TForeachAs;
use PhpStyler\Token\TForeachClosingBrace;
use PhpStyler\Token\TForeachClosingParen;
use PhpStyler\Token\TForeachDoubleArrow;
use PhpStyler\Token\TForeachOpeningBrace;
use PhpStyler\Token\TForeachOpeningParen;
use PhpStyler\Token\TForOpeningBrace;
use PhpStyler\Token\TForOpeningParen;
use PhpStyler\Token\TForSemicolon;
use PhpStyler\Token\TFullyQualifiedName;
use PhpStyler\Token\TFunction;
use PhpStyler\Token\TFunctionClosingBrace;
use PhpStyler\Token\TFunctionName;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TIf;
use PhpStyler\Token\TIfClosingBrace;
use PhpStyler\Token\TIfClosingParen;
use PhpStyler\Token\TIfColon;
use PhpStyler\Token\TIfOpeningBrace;
use PhpStyler\Token\TIfOpeningParen;
use PhpStyler\Token\TIndentDecrement;
use PhpStyler\Token\TIndentIncrement;
use PhpStyler\Token\TInt;
use PhpStyler\Token\TIntegerLiteral;
use PhpStyler\Token\TIsEqual;
use PhpStyler\Token\TMatch;
use PhpStyler\Token\TMatchClosingBrace;
use PhpStyler\Token\TMatchClosingParen;
use PhpStyler\Token\TMatchDoubleArrow;
use PhpStyler\Token\TMatchOpeningBrace;
use PhpStyler\Token\TMatchOpeningParen;
use PhpStyler\Token\TMatchReturnComma;
use PhpStyler\Token\TNamespace;
use PhpStyler\Token\TNamespaceEndSemicolon;
use PhpStyler\Token\TParamsClosingParen;
use PhpStyler\Token\TParamsComma;
use PhpStyler\Token\TParamsOpeningParen;
use PhpStyler\Token\TPhpOpeningTag;
use PhpStyler\Token\TPhpOpeningTagInline;
use PhpStyler\Token\TPostDecrement;
use PhpStyler\Token\TPostIncrement;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TQualifiedName;
use PhpStyler\Token\TReturnColon;
use PhpStyler\Token\TSemicolon;
use PhpStyler\Token\TSmallerThan;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplit;
use PhpStyler\Token\TString;
use PhpStyler\Token\TStringLiteral;
use PhpStyler\Token\TSwitch;
use PhpStyler\Token\TSwitchAfterCaseClosingBrace;
use PhpStyler\Token\TSwitchClosingParen;
use PhpStyler\Token\TSwitchOpeningBrace;
use PhpStyler\Token\TSwitchOpeningParen;
use PhpStyler\Token\TTry;
use PhpStyler\Token\TTryContinuationBrace;
use PhpStyler\Token\TTryOpeningBrace;
use PhpStyler\Token\TUnion;
use PhpStyler\Token\TUnqualifiedName;
use PhpStyler\Token\TUse;
use PhpStyler\Token\TUseEndSemicolon;
use PhpStyler\Token\TVariable;
use PhpStyler\Token\TVoid;
use PhpStyler\Token\TWhile;
use PhpStyler\Token\TWhileClosingBrace;
use PhpStyler\Token\TWhileClosingParen;
use PhpStyler\Token\TWhileOpeningBrace;
use PhpStyler\Token\TWhileOpeningParen;
use PHPUnit\Framework\Attributes\DataProvider;

class AssemblerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array<int, array<int, class-string>> $expect
     */
    #[DataProvider('provide')]
    public function test(string $code, array $expect) : void
    {
        $styler = new Styler(new Format\DeclarationFormat());
        $tokens = $styler->parse($code);
        $lines = $styler->assemble($tokens);
        $lines = $styler->split($lines);

        $actual = [];

        foreach ($lines as $line) {
            $classes = array_values(
                array_filter(
                    array_map(get_class(...), $line->getTokens()),
                    fn (string $class)
                        => $class !== TIndentIncrement::class
                            && $class !== TIndentDecrement::class
                            && $class !== TSpace::class
                            && ! is_a($class, TSplit::class, true),
                ),
            );

            $actual[] = $classes;
        }

        if (empty($expect)) {
            $message = 'Actual assembled lines:' . PHP_EOL;

            foreach ($actual as $i => $lineClasses) {
                $message .= "    Line {$i}:" . PHP_EOL;

                foreach ($lineClasses as $class) {
                    $parts = explode('\\', $class);
                    $message .= '        ' . end($parts) . '::class,' . PHP_EOL;
                }
            }

            $this->markTestIncomplete($message);
        } else {
            $this->assertSame($expect, $actual);
        }
    }

    /** @return array<string, array{0: string, 1: array<int, array<int, class-string>>}> */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'simple-statements' => [
                <<<'CODE'
                <?php
                    $foo = 'bar';
                    $baz = 'dib';
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TStringLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TStringLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'class-with-method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    public function bar() : void
                    {
                        $baz = 1;
                    }
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TClass::class,
                        TClassName::class,
                    ],
                    [
                        TClassOpeningBrace::class,
                    ],
                    [
                        TPublic::class,
                        TFunction::class,
                        TFunctionName::class,
                        TParamsOpeningParen::class,
                        TParamsClosingParen::class,
                        TReturnColon::class,
                        TVoid::class,
                    ],
                    [
                        TFunctionOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TFunctionClosingBrace::class,
                    ],
                    [
                        TClassClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'namespace-and-use' => [
                <<<'CODE'
                <?php
                namespace Foo;
                use Bar\Baz;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TNamespace::class,
                        TUnqualifiedName::class,
                        TNamespaceEndSemicolon::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TUse::class,
                        TQualifiedName::class,
                        TUseEndSemicolon::class,
                    ],
                ],
            ],
            'alt-syntax' => [
                <<<'CODE'
                <?php if ($a): $b = 1; endif;
                CODE,
                [
                    [
                        TPhpOpeningTagInline::class,
                        TIf::class,
                        TIfOpeningParen::class,
                        TVariable::class,
                        TIfClosingParen::class,
                        TIfColon::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TEndif::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'multiple-borders-per-source-line' => [
                <<<'CODE'
                <?php $a = 1; $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTagInline::class,
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'inline-comment-follows-border' => [
                <<<'CODE'
                <?php
                $a = 1; // comment
                $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'if-braces' => [
                <<<'CODE'
                <?php
                if ($foo == 1) {
                    $bar = 2;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TIf::class,
                        TIfOpeningParen::class,
                        TVariable::class,
                        TIsEqual::class,
                        TIntegerLiteral::class,
                        TIfClosingParen::class,
                        TIfOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TIfClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'function-with-params' => [
                <<<'CODE'
                <?php function foo(int $a, string $b) : void {}
                CODE,
                [
                    [
                        TPhpOpeningTagInline::class,
                        TFunction::class,
                        TFunctionName::class,
                        TParamsOpeningParen::class,
                        TInt::class,
                        TVariable::class,
                        TParamsComma::class,
                        TString::class,
                        TVariable::class,
                        TParamsClosingParen::class,
                        TReturnColon::class,
                        TVoid::class,
                    ],
                    [
                        TFunctionOpeningBrace::class,
                    ],
                    [
                        TFunctionClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'open-tag-only' => [
                <<<'CODE'
                <?php
                CODE,
                [
                    [
                        TPhpOpeningTagInline::class,
                    ],
                ],
            ],
            'slashed-inline-after-semicolon' => [
                <<<'CODE'
                <?php
                $a = 1; // slashed
                $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'hashed-inline-after-semicolon' => [
                <<<'CODE'
                <?php
                $a = 1; # hashed
                $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentHashedLineBreak::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'starred-inline-after-semicolon' => [
                <<<'CODE'
                <?php
                $a = 1; /* starred */
                $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentStarredLineBreak::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'starred-mid-expression' => [
                <<<'CODE'
                <?php
                $a = /* mid */ 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TCommentStarredMidStatement::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'slashed-own-line' => [
                <<<'CODE'
                <?php
                // own line
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TCommentSlashed::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'hashed-own-line' => [
                <<<'CODE'
                <?php
                # own line
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TCommentHashed::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'starred-oneline-own-line' => [
                <<<'CODE'
                <?php
                /* own line */
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TCommentStarred::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'starred-multiline-own-line' => [
                <<<'CODE'
                <?php
                /* multi
                   line */
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TCommentStarred::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'doccomment-oneline' => [
                <<<'CODE'
                <?php
                /** doc */
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TDocComment::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'doccomment-multiline' => [
                <<<'CODE'
                <?php
                /** doc
                 * comment */
                $a = 1;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TDocComment::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'blank-line-between-statements' => [
                <<<'CODE'
                <?php
                $a = 1;

                $b = 2;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'for-loop' => [
                <<<'CODE'
                <?php
                for ($i = 0; $i < 10; $i++) {
                    echo $i;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TFor::class,
                        TForOpeningParen::class,
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForSemicolon::class,
                        TVariable::class,
                        TSmallerThan::class,
                        TIntegerLiteral::class,
                        TForSemicolon::class,
                        TVariable::class,
                        TPostIncrement::class,
                        TForClosingParen::class,
                        TForOpeningBrace::class,
                    ],
                    [
                        TEcho::class,
                        TVariable::class,
                        TEchoEndSemicolon::class,
                    ],
                    [
                        TForClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'for-loop-with-comma-expressions' => [
                <<<'CODE'
                <?php
                for ($i = 0, $j = 10; $i < $j; $i++, $j--) {
                    echo $i;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TFor::class,
                        TForOpeningParen::class,
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForComma::class,
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForSemicolon::class,
                        TVariable::class,
                        TSmallerThan::class,
                        TVariable::class,
                        TForSemicolon::class,
                        TVariable::class,
                        TPostIncrement::class,
                        TForComma::class,
                        TVariable::class,
                        TPostDecrement::class,
                        TForClosingParen::class,
                        TForOpeningBrace::class,
                    ],
                    [
                        TEcho::class,
                        TVariable::class,
                        TEchoEndSemicolon::class,
                    ],
                    [
                        TForClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'for-loop-with-comments-and-blank-lines' => [
                <<<'CODE'
                <?php
                for (
                    $i = 0, // init i
                    $j = 10; // init j

                    $i < $j; // condition

                    $i++, // inc i
                    $j-- // dec j
                ) {
                    echo $i;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TFor::class,
                        TForOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForComma::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForSemicolon::class,
                    ],
                    [
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TVariable::class,
                        TSmallerThan::class,
                        TVariable::class,
                        TForSemicolon::class,
                    ],
                    [
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TVariable::class,
                        TPostIncrement::class,
                        TForComma::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TVariable::class,
                        TPostDecrement::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TForClosingParen::class,
                        TForOpeningBrace::class,
                    ],
                    [
                        TEcho::class,
                        TVariable::class,
                        TEchoEndSemicolon::class,
                    ],
                    [
                        TForClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'foreach-with-comments' => [
                <<<'CODE'
                <?php
                foreach (
                    $items // the items
                    as
                    $key => // the key
                    $value // the value
                ) {
                    echo $value;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TForeach::class,
                        TForeachOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                        TForeachAs::class,
                        TVariable::class,
                        TForeachDoubleArrow::class,
                        TCommentSlashedMidStatement::class,
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TForeachClosingParen::class,
                        TForeachOpeningBrace::class,
                    ],
                    [
                        TEcho::class,
                        TVariable::class,
                        TEchoEndSemicolon::class,
                    ],
                    [
                        TForeachClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'while-with-comments-and-blank-line' => [
                <<<'CODE'
                <?php
                while (
                    $i < 10 // condition

                ) {
                    $i++;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TWhile::class,
                        TWhileOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TSmallerThan::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TWhileClosingParen::class,
                        TWhileOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TPostIncrement::class,
                        TSemicolon::class,
                    ],
                    [
                        TWhileClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'if-with-comments-and-blank-line' => [
                <<<'CODE'
                <?php
                if (
                    $a == 1 // check a

                    || $b == 2 // check b
                ) {
                    echo $a;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TIf::class,
                        TIfOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TIsEqual::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TBooleanOr::class,
                        TVariable::class,
                    ],
                    [
                        TIsEqual::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TIfClosingParen::class,
                        TIfOpeningBrace::class,
                    ],
                    [
                        TEcho::class,
                        TVariable::class,
                        TEchoEndSemicolon::class,
                    ],
                    [
                        TIfClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'do-while-with-comments' => [
                <<<'CODE'
                <?php
                do {
                    $i++; // increment
                } while (
                    $i < 10 // condition
                );
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TDo::class,
                        TDoOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TPostIncrement::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TDoContinuationBrace::class,
                        TWhile::class,
                        TWhileOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                    ],
                    [
                        TSmallerThan::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TWhileClosingParen::class,
                        TDoWhileEndSemicolon::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'switch-with-comments-and-blank-line' => [
                <<<'CODE'
                <?php
                switch (
                    $a // the value

                ) {
                    case 1: // first
                        break;
                    default: // fallback
                        break;
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TSwitch::class,
                        TSwitchOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TSwitchClosingParen::class,
                        TSwitchOpeningBrace::class,
                    ],
                    [
                        TCase::class,
                        TIntegerLiteral::class,
                        TCaseColon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TBreak::class,
                        TSemicolon::class,
                    ],
                    [
                        TDefaultAfterCase::class,
                        TCaseColon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TBreak::class,
                        TSemicolon::class,
                    ],
                    [
                        TSwitchAfterCaseClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'match-with-comments-and-blank-line' => [
                <<<'CODE'
                <?php
                $x = match (
                    $a // the value

                ) {
                    1 => // one
                        "one",
                    default => // fallback
                        "other",
                };
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TMatch::class,
                        TMatchOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TMatchClosingParen::class,
                        TMatchOpeningBrace::class,
                    ],
                    [
                        TIntegerLiteral::class,
                        TMatchDoubleArrow::class,
                        TCommentSlashedMidStatement::class,
                        TStringLiteral::class,
                        TMatchReturnComma::class,
                    ],
                    [
                        TDefaultMatch::class,
                        TMatchDoubleArrow::class,
                        TCommentSlashedMidStatement::class,
                        TStringLiteral::class,
                        TMatchReturnComma::class,
                    ],
                    [
                        TMatchClosingBrace::class,
                        TSemicolon::class,
                    ],
                ],
            ],
            'try-catch-finally-with-comments' => [
                <<<'CODE'
                <?php
                try {
                    $a = 1; // try body
                } catch (
                    \RuntimeException // first
                    | \LogicException $e // second
                ) {
                    $b = 2; // catch body
                } finally {
                    $c = 3; // finally body
                }
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TTry::class,
                        TTryOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TTryContinuationBrace::class,
                        TCatch::class,
                        TParamsOpeningParen::class,
                    ],
                    [
                        TFullyQualifiedName::class,
                        TCommentSlashedMidStatement::class,
                        TUnion::class,
                        TFullyQualifiedName::class,
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TParamsClosingParen::class,
                        TCatchOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TCatchContinuationBrace::class,
                        TFinally::class,
                        TFinallyOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedLineBreak::class,
                    ],
                    [
                        TFinallyClosingBrace::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                ],
            ],
            'multiline-expression-with-comments-and-blank-lines' => [
                <<<'CODE'
                <?php
                $a = 1
                    + 2 // add two
                    + /* three */ 3

                    + 4
                    + 5;
                CODE,
                [
                    [
                        TPhpOpeningTag::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                    ],
                    [
                        TBinaryPlus::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                    ],
                    [
                        TBinaryPlus::class,
                        TCommentStarredMidStatement::class,
                        TIntegerLiteral::class,
                    ],
                    [
                        TBlankLine::class,
                    ],
                    [
                        TBinaryPlus::class,
                        TIntegerLiteral::class,
                        TBinaryPlus::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                    ],
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace Oxford;

use Oxford\Token\T;
use Oxford\Token\TIndentDecrement;
use Oxford\Token\TIndentIncrement;
use Oxford\Token\TSpace;
use Oxford\Token\TSplitPoint;
use Oxford\Token\TForeachAs;
use Oxford\Token\TAssign;
use Oxford\Token\TBinaryPlus;
use Oxford\Token\TBooleanOr;
use Oxford\Token\TBreak;
use Oxford\Token\TCase;
use Oxford\Token\TCatch;
use Oxford\Token\TClass;
use Oxford\Token\TClassClosingBrace;
use Oxford\Token\TClosingBraceDo;
use Oxford\Token\TForClosingBrace;
use Oxford\Token\TForeachClosingBrace;
use Oxford\Token\TFunctionClosingBrace;
use Oxford\Token\TIfClosingBrace;
use Oxford\Token\TMatchClosingBrace;
use Oxford\Token\TSwitchAfterCaseClosingBrace;
use Oxford\Token\TSwitchClosingBrace;
use Oxford\Token\TCatchClosingBrace;
use Oxford\Token\TFinallyClosingBrace;
use Oxford\Token\TClosingBraceTry;
use Oxford\Token\TWhileClosingBrace;
use Oxford\Token\TIfClosingParen;
use Oxford\Token\TMatchClosingParen;
use Oxford\Token\TSwitchClosingParen;
use Oxford\Token\TForClosingParen;
use Oxford\Token\TForOpeningParen;
use Oxford\Token\TForeachClosingParen;
use Oxford\Token\TForeachOpeningParen;
use Oxford\Token\TWhileClosingParen;
use Oxford\Token\TWhileOpeningParen;
use Oxford\Token\TParamsClosingParen;
use Oxford\Token\TCaseColon;
use Oxford\Token\TIfColon;
use Oxford\Token\TReturnColon;
use Oxford\Token\TForComma;
use Oxford\Token\TMatchReturnComma;
use Oxford\Token\TParamsComma;
use Oxford\Token\TCommentHashed;
use Oxford\Token\TCommentHashedInline;
use Oxford\Token\TCommentHashedMidStatement;
use Oxford\Token\TCommentHashedOwnLine;
use Oxford\Token\TCommentSlashed;
use Oxford\Token\TCommentSlashedInline;
use Oxford\Token\TCommentSlashedMidStatement;
use Oxford\Token\TCommentSlashedOwnLine;
use Oxford\Token\TCommentStarred;
use Oxford\Token\TCommentStarredInline;
use Oxford\Token\TCommentStarredOneline;
use Oxford\Token\TPostDecrement;
use Oxford\Token\TDefaultAfterCase;
use Oxford\Token\TDefaultCase;
use Oxford\Token\TDefaultMatch;
use Oxford\Token\TDo;
use Oxford\Token\TDocComment;
use Oxford\Token\TDocCommentOneline;
use Oxford\Token\TForeachDoubleArrow;
use Oxford\Token\TMatchDoubleArrow;
use Oxford\Token\TEcho;
use Oxford\Token\TEndif;
use Oxford\Token\TExtends;
use Oxford\Token\TFinally;
use Oxford\Token\TFor;
use Oxford\Token\TForeach;
use Oxford\Token\TFunction;
use Oxford\Token\TFunctionName;
use Oxford\Token\TIf;
use Oxford\Token\TPostIncrement;
use Oxford\Token\TInt;
use Oxford\Token\TIntegerLiteral;
use Oxford\Token\TIsEqual;
use Oxford\Token\TMatch;
use Oxford\Token\TFullyQualifiedName;
use Oxford\Token\TQualifiedName;
use Oxford\Token\TNamespace;
use Oxford\Token\TClassName;
use Oxford\Token\TUnqualifiedName;
use Oxford\Token\TClassOpeningBrace;
use Oxford\Token\TDoOpeningBrace;
use Oxford\Token\TForOpeningBrace;
use Oxford\Token\TForeachOpeningBrace;
use Oxford\Token\TFunctionOpeningBrace;
use Oxford\Token\TIfOpeningBrace;
use Oxford\Token\TMatchOpeningBrace;
use Oxford\Token\TSwitchOpeningBrace;
use Oxford\Token\TCatchOpeningBrace;
use Oxford\Token\TFinallyOpeningBrace;
use Oxford\Token\TTryOpeningBrace;
use Oxford\Token\TWhileOpeningBrace;
use Oxford\Token\TIfOpeningParen;
use Oxford\Token\TMatchOpeningParen;
use Oxford\Token\TSwitchOpeningParen;
use Oxford\Token\TParamsOpeningParen;
use Oxford\Token\TPhpOpeningTag;
use Oxford\Token\TPhpOpeningTagInline;
use Oxford\Token\TCatchContinuationBrace;
use Oxford\Token\TDoContinuationBrace;
use Oxford\Token\TTryContinuationBrace;
use Oxford\Token\TPublic;
use Oxford\Token\TSemicolon;
use Oxford\Token\TEchoEndSemicolon;
use Oxford\Token\TNamespaceEndSemicolon;
use Oxford\Token\TUseEndSemicolon;
use Oxford\Token\TForSemicolon;
use Oxford\Token\TSmallerThan;
use Oxford\Token\TString;
use Oxford\Token\TStringLiteral;
use Oxford\Token\TSwitch;
use Oxford\Token\TTry;
use Oxford\Token\TUnion;
use Oxford\Token\TUse;
use Oxford\Token\TVariable;
use Oxford\Token\TVoid;
use Oxford\Token\TWhile;
use Oxford\Token\TBlankLine;

class AssemblerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array<int, array<int, class-string>> $expect
     * @dataProvider provide
     */
    public function test(string $code, array $expect) : void
    {
        $styler = new Styler();
        $tokens = $styler->parse($code);
        $lines = $styler->assemble($tokens);
        $lines = $styler->split($lines);

        $actual = [];

        foreach ($lines as $line) {
            $classes = array_values(array_filter(
                $line->getTokenClasses(),
                fn(string $class) => $class !== TIndentIncrement::class && $class !== TIndentDecrement::class && $class !== TSpace::class && ! is_a($class, TSplitPoint::class, true),
            ));
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
                        TCommentSlashedInline::class,
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
                        TCommentSlashedInline::class,
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
                        TCommentHashedInline::class,
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
                        TCommentStarredInline::class,
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
                        TCommentStarredInline::class,
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
                        TCommentSlashedOwnLine::class,
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
                        TCommentHashedOwnLine::class,
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
                        TCommentStarredOneline::class,
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
                        TDocCommentOneline::class,
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
                        TFor::class,
                        TForOpeningParen::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TForComma::class,
                        TCommentSlashedMidStatement::class,
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
                        TForeach::class,
                        TForeachOpeningParen::class,
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                        TForeachAs::class,
                        TVariable::class,
                        TForeachDoubleArrow::class,
                        TCommentSlashedMidStatement::class,
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
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
                        TCommentSlashedInline::class,
                    ],
                    [
                        TDoContinuationBrace::class,
                        TWhile::class,
                        TWhileOpeningParen::class,
                        TVariable::class,
                        TSmallerThan::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                        TWhileClosingParen::class,
                        TSemicolon::class,
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
                        TCommentSlashedInline::class,
                    ],
                    [
                        TBreak::class,
                        TSemicolon::class,
                    ],
                    [
                        TDefaultAfterCase::class,
                        TCaseColon::class,
                        TCommentSlashedInline::class,
                    ],
                    [
                        TBreak::class,
                        TSemicolon::class,
                    ],
                    [
                        TSwitchAfterCaseClosingBrace::class,
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
                        TCommentSlashedInline::class,
                    ],
                    [
                        TTryContinuationBrace::class,
                        TCatch::class,
                        TParamsOpeningParen::class,
                        TFullyQualifiedName::class,
                        TCommentSlashedMidStatement::class,
                        TUnion::class,
                        TFullyQualifiedName::class,
                        TVariable::class,
                        TCommentSlashedMidStatement::class,
                        TParamsClosingParen::class,
                        TCatchOpeningBrace::class,
                    ],
                    [
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TSemicolon::class,
                        TCommentSlashedInline::class,
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
                        TCommentSlashedInline::class,
                    ],
                    [
                        TFinallyClosingBrace::class,
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
                        TVariable::class,
                        TAssign::class,
                        TIntegerLiteral::class,
                        TBinaryPlus::class,
                        TIntegerLiteral::class,
                        TCommentSlashedMidStatement::class,
                        TBinaryPlus::class,
                        TCommentStarredInline::class,
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

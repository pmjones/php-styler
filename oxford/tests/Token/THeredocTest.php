<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class THeredocTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'heredoc' => [
                <<<'CODE'
                <?php
                $foo = <<<HEREDOC
                foo {$bar->baz['dib']} zim
                HEREDOC;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    THeredocStart::class,
                    TStringFragment::class,
                    TCurlyOpen::class,
                    TVariable::class,
                    TEncapsedObjectOperator::class,
                    TPropertyAccessName::class,
                    TArrayElementOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayElementClosingBracket::class,
                    TCurlyClose::class,
                    TStringFragment::class,
                    THeredocEnd::class,
                    TSemicolon::class,
                ],
            ],
            'nowdoc' => [
                <<<'CODE'
                <?php
                $foo = <<<'NOWDOC'
                foo {$bar->baz['dib']} zim
                NOWDOC;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    THeredocStart::class,
                    TStringFragment::class,
                    THeredocEnd::class,
                    TSemicolon::class,
                ],
            ],
            'nowdoc-argument' => [
                <<<'CODE'
                <?php
                foo(
                    <<<'NOWDOC'
                    foo {$bar->baz['dib']} zim
                    NOWDOC,
                    $bar,
                );
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    THeredocStart::class,
                    TStringFragment::class,
                    THeredocEnd::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsComma::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

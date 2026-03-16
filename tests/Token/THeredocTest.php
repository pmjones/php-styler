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
        /** @php-styler-expansive */
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
                    TEncapsedVariable::class,
                    TEncapsedObjectOperator::class,
                    TEncapsedPropertyAccessName::class,
                    TEncapsedArrayElementOpeningBracket::class,
                    TStringLiteral::class,
                    TEncapsedArrayElementClosingBracket::class,
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

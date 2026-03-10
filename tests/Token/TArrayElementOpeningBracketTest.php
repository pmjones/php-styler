<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TArrayElementOpeningBracketTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'numeric' => [
                <<<'CODE'
                <?php
                $foo[0];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'string' => [
                <<<'CODE'
                <?php
                $foo['bar'];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'variable' => [
                <<<'CODE'
                <?php
                $foo[$bar];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TVariable::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'multiple' => [
                <<<'CODE'
                <?php
                $foo[0]['bar'][$baz];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayElementClosingBracket::class,
                    TArrayElementOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayElementClosingBracket::class,
                    TArrayElementOpeningBracket::class,
                    TVariable::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'property' => [
                <<<'CODE'
                <?php
                $foo->bar['baz'];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TArrayElementOpeningBracket::class,
                    TStringLiteral::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'dereferenced' => [
                <<<'CODE'
                <?php
                foo()[0];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TArrayElementOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayElementClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace Oxford\Token;

class TArrayTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'empty' => [
                <<<'CODE'
                <?php
                array();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayConstruct::class,
                    TArrayConstructOpeningParen::class,
                    TArrayConstructClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'list' => [
                <<<'CODE'
                <?php
                array('foo', 'bar', 'baz');
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayConstruct::class,
                    TArrayConstructOpeningParen::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayConstructClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'numeric-key' => [
                <<<'CODE'
                <?php
                array(1 => 'foo', 2 => 'bar', 3 => 'baz');
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayConstruct::class,
                    TArrayConstructOpeningParen::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayConstructClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'string-key' => [
                <<<'CODE'
                <?php
                array('foo' => 1, 'bar' => 2, 'baz' => 3);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayConstruct::class,
                    TArrayConstructOpeningParen::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TStringLiteral::class,
                    TArrayDoubleArrow::class,
                    TIntegerLiteral::class,
                    TArrayConstructClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'typehint' => [
                <<<'CODE'
                <?php
                function foo(array $bar) {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TArray::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}

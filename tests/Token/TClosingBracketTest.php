<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TClosingBracketTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'array' => [
                <<<'CODE'
                <?php
                $foo = [1, 2];
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'element' => [
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
            'attribute' => [
                <<<'CODE'
                <?php
                #[Foo]
                function bar() {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAttribute::class,
                    TUnqualifiedName::class,
                    TAttributeClosingBracket::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}

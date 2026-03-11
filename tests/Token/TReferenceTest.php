<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TReferenceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'pass-by-reference' => [
                <<<'CODE'
                <?php
                function foo(&$bar)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TReference::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'return-by-reference' => [
                <<<'CODE'
                <?php
                function &foo()
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TReference::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'closure-return-by-reference' => [
                <<<'CODE'
                <?php
                $foo = function &() {
                };
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFunction::class,
                    TReference::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TSemicolon::class,
                ],
            ],
            'arrow-return-by-reference' => [
                <<<'CODE'
                <?php
                $foo = fn &() => null;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFn::class,
                    TReference::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFnDoubleArrow::class,
                    TNull::class,
                    TFnEndSemicolon::class,
                ],
            ],
        ];
    }
}

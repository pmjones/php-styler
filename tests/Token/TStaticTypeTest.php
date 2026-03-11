<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticTypeTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'return-type' => [
                <<<'CODE'
                <?php
                function foo() : static
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TStaticType::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'nullable-return-type' => [
                <<<'CODE'
                <?php
                function foo() : ?static
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TReturnColon::class,
                    TNullable::class,
                    TStaticType::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}

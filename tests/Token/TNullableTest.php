<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNullableTest extends TTestCase
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
                function foo() : ?int {
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
                    TInt::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'parameter' => [
                '<?php function foo(?int $x) {}',
                [
                    TPhpOpeningTagInline::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TNullable::class,
                    TInt::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
            'property' => [
                '<?php class Foo { public ?int $x; }',
                [
                    TPhpOpeningTagInline::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TNullable::class,
                    TInt::class,
                    TVariable::class,
                    TPropertyEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
            'second-parameter' => [
                '<?php function foo(int $a, ?string $b) {}',
                [
                    TPhpOpeningTagInline::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TInt::class,
                    TVariable::class,
                    TParamsComma::class,
                    TNullable::class,
                    TString::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}

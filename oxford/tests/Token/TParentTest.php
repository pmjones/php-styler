<?php
declare(strict_types=1);

namespace Oxford\Token;

class TParentTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                class Foo extends Bar {
                    public function baz() {
                        parent::baz();
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TExtends::class,
                    TUnqualifiedName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TParent::class,
                    TMemberDoubleColon::class,
                    TStaticMethodCallName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

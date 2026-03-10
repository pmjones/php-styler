<?php
declare(strict_types=1);

namespace Oxford\Token;

class TProtectedTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'method' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    protected function bar()
                    {
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TProtected::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

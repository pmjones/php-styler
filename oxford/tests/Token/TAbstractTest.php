<?php
declare(strict_types=1);

namespace Oxford\Token;

class TAbstractTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'class-or-method' => [
                <<<'CODE'
                <?php
                abstract class Foo
                {
                    abstract function bar();
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAbstract::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TAbstract::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TParamsClosingParen::class,
                    TAbstractMethodEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

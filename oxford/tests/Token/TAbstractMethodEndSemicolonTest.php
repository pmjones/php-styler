<?php
declare(strict_types=1);

namespace Oxford\Token;

class TAbstractMethodEndSemicolonTest extends TTestCase
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
                abstract class Foo {
                    abstract public function bar();
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TAbstract::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TAbstract::class,
                    TPublic::class,
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

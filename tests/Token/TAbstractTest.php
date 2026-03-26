<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAbstractTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
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

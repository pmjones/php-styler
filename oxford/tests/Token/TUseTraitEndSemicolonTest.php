<?php
declare(strict_types=1);

namespace Oxford\Token;

class TUseTraitEndSemicolonTest extends TTestCase
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
                class Foo {
                    use Bar;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TUseTrait::class,
                    TUnqualifiedName::class,
                    TUseTraitEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

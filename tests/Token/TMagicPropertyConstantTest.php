<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicPropertyConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        get => __PROPERTY__;
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TPublic::class,
                    TString::class,
                    TVariable::class,
                    TPropertyHooksOpeningBrace::class,
                    TPropertyHookGet::class,
                    TPropertyHookGetDoubleArrow::class,
                    TMagicPropertyConstant::class,
                    TPropertyHookGetSemicolon::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace Oxford\Token;

class TPropertyHookGetTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'bodied-get' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        get {
                            return $this->bar;
                        }
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
                    TPropertyHookGetOpeningBrace::class,
                    TReturn::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                    TPropertyHookGetClosingBrace::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'shorthand-get' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        get => $this->bar;
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
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TPropertyHookGetSemicolon::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

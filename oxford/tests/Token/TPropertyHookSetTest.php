<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyHookSetTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'bodied-set' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        set($value) {
                            $this->bar = $value;
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
                    TPropertyHookSet::class,
                    TPropertyHookSetOpeningParen::class,
                    TVariable::class,
                    TPropertyHookSetClosingParen::class,
                    TPropertyHookSetOpeningBrace::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TPropertyHookSetClosingBrace::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'shorthand-set' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        set => $value;
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
                    TPropertyHookSet::class,
                    TPropertyHookSetDoubleArrow::class,
                    TVariable::class,
                    TPropertyHookSetSemicolon::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

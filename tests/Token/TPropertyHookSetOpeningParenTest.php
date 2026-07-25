<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PHPUnit\Framework\Attributes\RequiresPhp;

#[RequiresPhp('>=8.4')]
class TPropertyHookSetOpeningParenTest extends TTestCase
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
                    public int $bar {
                        set(int $value) {
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
                    TInt::class,
                    TVariable::class,
                    TPropertyHooksOpeningBrace::class,
                    TPropertyHookSet::class,
                    TPropertyHookSetOpeningParen::class,
                    TInt::class,
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
            'nullable-param-type' => [
                <<<'CODE'
                <?php
                class Foo {
                    public ?string $bar {
                        set(?string $value) {
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
                    TNullable::class,
                    TString::class,
                    TVariable::class,
                    TPropertyHooksOpeningBrace::class,
                    TPropertyHookSet::class,
                    TPropertyHookSetOpeningParen::class,
                    TNullable::class,
                    TString::class,
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
            'union-param-type' => [
                <<<'CODE'
                <?php
                class Foo {
                    public Bar|Baz $bar {
                        set(Bar|Baz $value) {
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
                    TUnqualifiedName::class,
                    TUnion::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyHooksOpeningBrace::class,
                    TPropertyHookSet::class,
                    TPropertyHookSetOpeningParen::class,
                    TUnqualifiedName::class,
                    TUnion::class,
                    TUnqualifiedName::class,
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
            'intersection-param-type' => [
                <<<'CODE'
                <?php
                class Foo {
                    public Bar&Baz $bar {
                        set(Bar&Baz $value) {
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
                    TUnqualifiedName::class,
                    TIntersection::class,
                    TUnqualifiedName::class,
                    TVariable::class,
                    TPropertyHooksOpeningBrace::class,
                    TPropertyHookSet::class,
                    TPropertyHookSetOpeningParen::class,
                    TUnqualifiedName::class,
                    TIntersection::class,
                    TUnqualifiedName::class,
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
        ];
    }
}

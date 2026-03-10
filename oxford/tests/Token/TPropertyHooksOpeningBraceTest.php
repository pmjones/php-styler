<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyHooksOpeningBraceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'get-and-set' => [
                <<<'CODE'
                <?php
                class Foo {
                    public string $bar {
                        get {
                            return $this->bar;
                        }
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
                    TPropertyHookGet::class,
                    TPropertyHookGetOpeningBrace::class,
                    TReturn::class,
                    TVariable::class,
                    TObjectOperator::class,
                    TPropertyAccessName::class,
                    TSemicolon::class,
                    TPropertyHookGetClosingBrace::class,
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
        ];
    }
}

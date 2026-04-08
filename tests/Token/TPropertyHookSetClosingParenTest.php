<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PHPUnit\Framework\Attributes\RequiresPhp;

#[RequiresPhp('>=8.4')]
class TPropertyHookSetClosingParenTest extends TTestCase
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
        ];
    }
}

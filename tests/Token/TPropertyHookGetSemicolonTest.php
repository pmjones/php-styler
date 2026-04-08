<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PHPUnit\Framework\Attributes\RequiresPhp;

#[RequiresPhp('>=8.4')]
class TPropertyHookGetSemicolonTest extends TTestCase
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
                        get => 1;
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
                    TPropertyHookGet::class,
                    TPropertyHookGetDoubleArrow::class,
                    TIntegerLiteral::class,
                    TPropertyHookGetSemicolon::class,
                    TPropertyHooksClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

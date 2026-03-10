<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TConstantNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'file-const' => [
                <<<'CODE'
                <?php
                const FOO = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TNamespaceConstEndSemicolon::class,
                ],
            ],
            'class-const' => [
                <<<'CODE'
                <?php
                class Foo {
                    const BAR = 1;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TConst::class,
                    TConstantName::class,
                    TAssignConst::class,
                    TIntegerLiteral::class,
                    TConstEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

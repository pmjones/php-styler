<?php
declare(strict_types=1);

namespace Oxford\Token;

class TConstTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'const' => [
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
                class Foo
                {
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
            'use-const-import' => [
                <<<'CODE'
                <?php
                use const Foo\BAR;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseConst::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
        ];
    }
}

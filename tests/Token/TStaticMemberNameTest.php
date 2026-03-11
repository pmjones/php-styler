<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStaticMemberNameTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'class-constant' => [
                <<<'CODE'
                <?php
                static::class;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TStaticBinding::class,
                    TMemberDoubleColon::class,
                    TStaticMemberName::class,
                    TSemicolon::class,
                ],
            ],
            'trait-method-ref' => [
                <<<'CODE'
                <?php
                class Foo {
                    use A, B {
                        B::smallTalk insteadof A;
                    }
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TUseTrait::class,
                    TUnqualifiedName::class,
                    TUseTraitComma::class,
                    TUnqualifiedName::class,
                    TUseTraitOpeningBrace::class,
                    TUnqualifiedName::class,
                    TUseTraitDoubleColon::class,
                    TStaticMemberName::class,
                    TInsteadof::class,
                    TUnqualifiedName::class,
                    TSemicolon::class,
                    TUseTraitClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

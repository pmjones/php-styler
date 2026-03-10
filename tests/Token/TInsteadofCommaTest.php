<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TInsteadofCommaTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'insteadof-multiple' => [
                <<<'CODE'
                <?php
                class Foo {
                    use A, B {
                        A::method insteadof B, C;
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
                    TInsteadofComma::class,
                    TUnqualifiedName::class,
                    TSemicolon::class,
                    TUseTraitClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

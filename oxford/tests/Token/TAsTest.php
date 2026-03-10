<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TAsTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'foreach' => [
                <<<'CODE'
                <?php
                foreach ($foo as $bar)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TForeach::class,
                    TForeachOpeningParen::class,
                    TVariable::class,
                    TForeachAs::class,
                    TVariable::class,
                    TForeachClosingParen::class,
                    TForeachOpeningBrace::class,
                    TForeachClosingBrace::class,
                ],
            ],
            'import-alias' => [
                <<<'CODE'
                <?php
                use Foo\Bar as Baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseAs::class,
                    TUseAlias::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'trait-alias' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar {
                        Bar::baz as dib;
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
                    TUseTraitOpeningBrace::class,
                    TUnqualifiedName::class,
                    TUseTraitDoubleColon::class,
                    TStaticMemberName::class,
                    TUseTraitAs::class,
                    TTraitAlias::class,
                    TSemicolon::class,
                    TUseTraitClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
            'trait-visibility' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar {
                        Bar::baz as public;
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
                    TUseTraitOpeningBrace::class,
                    TUnqualifiedName::class,
                    TUseTraitDoubleColon::class,
                    TStaticMemberName::class,
                    TUseTraitAs::class,
                    TPublic::class,
                    TSemicolon::class,
                    TUseTraitClosingBrace::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TTraitAliasTest extends TTestCase
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
        ];
    }
}

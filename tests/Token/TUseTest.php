<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUseTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'import' => [
                <<<'CODE'
                <?php
                use Foo;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUnqualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'trait' => [
                <<<'CODE'
                <?php
                class Foo
                {
                    use Bar;
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TClass::class,
                    TClassName::class,
                    TClassOpeningBrace::class,
                    TUseTrait::class,
                    TUnqualifiedName::class,
                    TUseTraitEndSemicolon::class,
                    TClassClosingBrace::class,
                ],
            ],
        ];
    }
}

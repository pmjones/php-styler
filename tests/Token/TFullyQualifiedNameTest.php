<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFullyQualifiedNameTest extends TTestCase
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
                $foo = new \App\Bar();
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TNew::class,
                    TFullyQualifiedName::class,
                    TArgsOpeningParen::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
            'use-leading-backslash' => [
                <<<'CODE'
                <?php
                use \Foo\Bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'use-function-leading-backslash' => [
                <<<'CODE'
                <?php
                use function \Foo\bar;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TUse::class,
                    TUseFunction::class,
                    TQualifiedName::class,
                    TUseEndSemicolon::class,
                ],
            ],
            'use-const-leading-backslash' => [
                <<<'CODE'
                <?php
                use const \Foo\BAR;
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

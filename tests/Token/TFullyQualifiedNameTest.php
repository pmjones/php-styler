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
        ];
    }
}

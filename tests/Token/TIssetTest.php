<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TIssetTest extends TTestCase
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
                isset($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TIsset::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

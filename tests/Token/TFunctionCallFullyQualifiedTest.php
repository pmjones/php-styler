<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFunctionCallFullyQualifiedTest extends TTestCase
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
                \array_map($foo, $bar);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunctionCallFullyQualified::class,
                    TArgsOpeningParen::class,
                    TVariable::class,
                    TArgsComma::class,
                    TVariable::class,
                    TArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForeachClosingBracelessTest extends TTestCase
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
                foreach ($foo as $bar)
                    $baz = $bar;
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
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                    TForeachClosingBrace::class,
                ],
            ],
        ];
    }
}

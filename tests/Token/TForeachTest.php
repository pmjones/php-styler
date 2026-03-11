<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForeachTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'braced' => [
                <<<'CODE'
                <?php
                foreach ($foo as $bar) {
                    $baz ++;
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
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TForeachClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                foreach ($foo as $bar):
                    $baz ++;
                endforeach;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TForeach::class,
                    TForeachOpeningParen::class,
                    TVariable::class,
                    TForeachAs::class,
                    TVariable::class,
                    TForeachClosingParen::class,
                    TForeachColon::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TEndforeach::class,
                    TSemicolon::class,
                ],
            ],
            'unbraced' => [
                <<<'CODE'
                <?php
                foreach ($foo as $bar) $baz ++;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TForeach::class,
                    TForeachOpeningParen::class,
                    TVariable::class,
                    TForeachAs::class,
                    TVariable::class,
                    TForeachClosingParen::class,
                    TOpeningBraceless::class,
                    TVariable::class,
                    TPostIncrement::class,
                    TSemicolon::class,
                    TForeachClosingBraceless::class,
                ],
            ],
        ];
    }
}

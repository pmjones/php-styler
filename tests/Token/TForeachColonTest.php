<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TForeachColonTest extends TTestCase
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
                foreach ($foo as $bar):
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
                    TEndforeach::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEndforeachTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'endforeach' => [
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

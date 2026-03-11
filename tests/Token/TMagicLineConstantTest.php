<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicLineConstantTest extends TTestCase
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
                $foo = __LINE__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicLineConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

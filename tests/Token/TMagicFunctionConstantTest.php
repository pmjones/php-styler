<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicFunctionConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'magic-constant' => [
                <<<'CODE'
                <?php
                $foo = __FUNCTION__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicFunctionConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

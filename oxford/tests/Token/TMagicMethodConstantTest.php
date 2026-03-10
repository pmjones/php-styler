<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicMethodConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = __METHOD__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicMethodConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

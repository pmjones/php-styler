<?php
declare(strict_types=1);

namespace Oxford\Token;

class TMagicFileConstantTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'magic-constant' => [
                <<<'CODE'
                <?php
                $foo = __FILE__;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TMagicFileConstant::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

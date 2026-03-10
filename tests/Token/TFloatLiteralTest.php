<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFloatLiteralTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'float' => [
                <<<'CODE'
                <?php
                $foo = 3.14;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFloatLiteral::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

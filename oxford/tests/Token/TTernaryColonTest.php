<?php
declare(strict_types=1);

namespace Oxford\Token;

class TTernaryColonTest extends TTestCase
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
                $foo = $bar ? 1 : 2;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TTernaryQuestion::class,
                    TIntegerLiteral::class,
                    TTernaryColon::class,
                    TIntegerLiteral::class,
                    TTernaryEndSemicolon::class,
                ],
            ],
        ];
    }
}

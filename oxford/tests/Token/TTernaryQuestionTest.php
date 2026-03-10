<?php
declare(strict_types=1);

namespace Oxford\Token;

class TTernaryQuestionTest extends TTestCase
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
                $foo = $bar ? $baz : $dib;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TTernaryQuestion::class,
                    TVariable::class,
                    TTernaryColon::class,
                    TVariable::class,
                    TTernaryEndSemicolon::class,
                ],
            ],
        ];
    }
}

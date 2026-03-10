<?php
declare(strict_types=1);

namespace Oxford\Token;

class TElvisQuestionTest extends TTestCase
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
                $foo = $bar ?: $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TVariable::class,
                    TElvisQuestion::class,
                    TElvisColon::class,
                    TVariable::class,
                    TElvisEndSemicolon::class,
                ],
            ],
        ];
    }
}

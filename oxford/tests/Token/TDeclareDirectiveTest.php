<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDeclareDirectiveTest extends TTestCase
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
                declare(ticks=1);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDeclare::class,
                    TDeclareDirectivesOpeningParen::class,
                    TDeclareDirective::class,
                    TAssignDirective::class,
                    TIntegerLiteral::class,
                    TDeclareDirectivesClosingParen::class,
                    TDeclareEndSemicolon::class,
                ],
                [],
            ],
        ];
    }
}

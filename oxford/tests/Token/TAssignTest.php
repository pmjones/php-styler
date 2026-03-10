<?php
declare(strict_types=1);

namespace Oxford\Token;

class TAssignTest extends TTestCase
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
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                ],
            ],
            'declare-directive' => [
                <<<'CODE'
                <?php
                declare(strict_types=1);
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

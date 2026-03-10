<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDeclareTest extends TTestCase
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
            'multiple' => [
                <<<'CODE'
                <?php
                declare(strict_types=1, ticks=1);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDeclare::class,
                    TDeclareDirectivesOpeningParen::class,
                    TDeclareDirective::class,
                    TAssignDirective::class,
                    TIntegerLiteral::class,
                    TDeclareDirectivesComma::class,
                    TDeclareDirective::class,
                    TAssignDirective::class,
                    TIntegerLiteral::class,
                    TDeclareDirectivesClosingParen::class,
                    TDeclareEndSemicolon::class,
                ],
                [],
            ],
            'braced' => [
                <<<'CODE'
                <?php
                declare(strict_types=1)
                {
                }
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDeclare::class,
                    TDeclareDirectivesOpeningParen::class,
                    TDeclareDirective::class,
                    TAssignDirective::class,
                    TIntegerLiteral::class,
                    TDeclareDirectivesClosingParen::class,
                    TDeclareOpeningBrace::class,
                    TDeclareClosingBrace::class,
                ],
            ],
            'alternative' => [
                <<<'CODE'
                <?php
                declare(ticks=1):
                enddeclare;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDeclare::class,
                    TDeclareDirectivesOpeningParen::class,
                    TDeclareDirective::class,
                    TAssignDirective::class,
                    TIntegerLiteral::class,
                    TDeclareDirectivesClosingParen::class,
                    TDeclareColon::class,
                    TEnddeclare::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

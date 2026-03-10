<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDeclareEndSemicolonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'declare-semicolon' => [
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
            ],
        ];
    }
}

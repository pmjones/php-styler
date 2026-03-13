<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDeclareColonTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                declare(strict_types=1):
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

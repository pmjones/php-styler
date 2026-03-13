<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDeclareOpeningBraceTest extends TTestCase
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
                declare(strict_types=1) {}
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
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDeclareDirectivesCommaTest extends TTestCase
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
                declare(strict_types=1, encoding='UTF-8') {}
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
                    TStringLiteral::class,
                    TDeclareDirectivesClosingParen::class,
                    TDeclareOpeningBrace::class,
                    TDeclareClosingBrace::class,
                ],
            ],
        ];
    }
}

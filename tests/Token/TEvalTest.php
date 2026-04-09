<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEvalTest extends TTestCase
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
                eval($code);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEval::class,
                    TKeywordArgsOpeningParen::class,
                    TVariable::class,
                    TKeywordArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

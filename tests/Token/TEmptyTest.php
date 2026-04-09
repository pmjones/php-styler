<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEmptyTest extends TTestCase
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
                empty($foo);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEmpty::class,
                    TKeywordArgsOpeningParen::class,
                    TVariable::class,
                    TKeywordArgsClosingParen::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

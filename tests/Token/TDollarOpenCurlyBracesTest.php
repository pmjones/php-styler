<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDollarOpenCurlyBracesTest extends TTestCase
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
                "${foo}";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TDoubleQuote::class,
                    TDollarOpenCurlyBraces::class,
                    TStringVarname::class,
                    TDollarCloseCurlyBraces::class,
                    TDoubleQuote::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

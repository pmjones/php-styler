<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDollarOpenCurlyBracesTest extends TTestCase
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

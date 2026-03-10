<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDollarCloseCurlyBracesTest extends TTestCase
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
                echo "${foo}";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TDoubleQuote::class,
                    TDollarOpenCurlyBraces::class,
                    TStringVarname::class,
                    TDollarCloseCurlyBraces::class,
                    TDoubleQuote::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringVarnameTest extends TTestCase
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
                echo "${foo}";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TDoubleQuoteOpening::class,
                    TDollarOpenCurlyBraces::class,
                    TStringVarname::class,
                    TDollarCloseCurlyBraces::class,
                    TDoubleQuoteClosing::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}

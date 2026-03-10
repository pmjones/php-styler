<?php
declare(strict_types=1);

namespace Oxford\Token;

class TDoubleQuoteTest extends TTestCase
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
                echo "$foo";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TDoubleQuote::class,
                    TVariable::class,
                    TDoubleQuote::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}

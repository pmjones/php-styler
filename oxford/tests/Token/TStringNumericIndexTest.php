<?php
declare(strict_types=1);

namespace Oxford\Token;

class TStringNumericIndexTest extends TTestCase
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
                echo "$foo[0]";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TDoubleQuote::class,
                    TVariable::class,
                    TArrayElementOpeningBracket::class,
                    TStringNumericIndex::class,
                    TArrayElementClosingBracket::class,
                    TDoubleQuote::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}

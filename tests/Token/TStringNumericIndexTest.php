<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TStringNumericIndexTest extends TTestCase
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
                echo "$foo[0]";
                CODE,
                [
                    TPhpOpeningTag::class,
                    TEcho::class,
                    TDoubleQuote::class,
                    TEncapsedVariable::class,
                    TEncapsedArrayElementOpeningBracket::class,
                    TStringNumericIndex::class,
                    TEncapsedArrayElementClosingBracket::class,
                    TDoubleQuote::class,
                    TEchoEndSemicolon::class,
                ],
            ],
        ];
    }
}

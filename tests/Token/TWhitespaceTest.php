<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TWhitespaceTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'space-newline' => [
                "<?php 1;    \n    2;\n",
                [
                    TPhpOpeningTagInline::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                ],
                [],
                self::REPORT_WHITESPACE,

            ],
            'newline-tab' => [
                "<?php\n\t1;\n\t2;\n",
                [
                    TPhpOpeningTag::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                ],
                [],
                self::REPORT_WHITESPACE,

            ],
            'newline-space' => [
                "<?php\n    1;\n    2;\n    3;\n",
                [
                    TPhpOpeningTag::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                    TIntegerLiteral::class,
                    TSemicolon::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            ],
        ];
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPhpOpeningTagTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        return [
            'plain' => [
                "<?php",
                [
                    TPhpOpeningTagInline::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'space' => [
                "<?php ",
                [
                    TPhpOpeningTagInline::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'spaces' => [
                "<?php    ",
                [
                    TPhpOpeningTagInline::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'tab' => [
                "<?php\t",
                [
                    TPhpOpeningTagInline::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'tabs' => [
                "<?php\t\t\t\t",
                [
                    TPhpOpeningTagInline::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'newline' => [
                "<?php\n",
                [
                    TPhpOpeningTag::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'newlines' => [
                "<?php\n\n\n\n",
                [
                    TPhpOpeningTag::class,
                    TBlankLine::class,
                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'newline-and-spaces' => [
                "<?php\n    ",
                [
                    TPhpOpeningTag::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            ],
            'newline-and-tabs' => [
                "<?php\n\t\t\t\t",
                [
                    TPhpOpeningTag::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            ],
        ];
    }
}

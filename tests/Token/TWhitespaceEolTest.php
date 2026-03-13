<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TWhitespaceEolTest extends TTestCase
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
                $foo = 1;
                CODE,
                [
                    TPhpOpeningTag::class,

                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                
                ],
                [],
                self::REPORT_WHITESPACE,
            
            ],
            'blank-line' => [
                "<?php\n\$a = 1;\n\n\$b = 2;\n",
                [
                    TPhpOpeningTag::class,

                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TBlankLine::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            
            ],
            'blank-line-with-indent' => [
                "<?php\n\$a = 1;\n    \n\$b = 2;\n",
                [
                    TPhpOpeningTag::class,

                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,
                    TBlankLine::class,
                    TVariable::class,
                    TAssign::class,
                    TIntegerLiteral::class,
                    TSemicolon::class,

                ],
                [],
                self::REPORT_WHITESPACE,
            ],
        ];
    }
}

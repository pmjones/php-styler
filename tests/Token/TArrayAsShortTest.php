<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpStyler\TestFormat;

class TArrayAsShortTest extends TTestCase
{
    /**
     * @dataProvider provide
     */
    public function test(
        string $code,
        array $expect,
        array $finalNesting = [],
        string $reporting = self::IGNORE_WHITESPACE,
    ) : void
    {
        $parser = new Parser(
            new TestFormat(parseAs: [TArray::class => TArrayAsShort::class]),
        );

        $tokens = $parser($code);

        $actual = [];

        $skip = match ($reporting) {
            self::REPORT_WHITESPACE => [
                TSpace::class,
                TIndentIncrement::class,
                TIndentDecrement::class,
                TLineBreak::class,
            ],
            self::REPORT_SYNTHETIC => [
                TWhitespace::class,
                TBlankLine::class,
                TSpace::class,
            ],
            self::REPORT_LINEBREAKS => [
                TWhitespace::class,
                TSpace::class,
                TIndentIncrement::class,
                TIndentDecrement::class,
                TLineBreak::class,
            ],
            self::IGNORE_WHITESPACE => [
                TWhitespace::class,
                TBlankLine::class,
                TSpace::class,
                TIndentIncrement::class,
                TIndentDecrement::class,
                TLineBreak::class,
            ],
            default => [],
        };

        foreach ($tokens as $token) {
            if ($token instanceof TSplit || in_array(get_class($token), $skip)) {
                continue;
            }

            /** @var class-string */
            $class = get_class($token);
            $actual[] = $class;
        }

        if (empty($expect)) {
            $message = 'Actual token classes:' . PHP_EOL;

            foreach ($actual as $class) {
                $parts = explode('\\', $class);
                $message .= '                    '
                    . end($parts)
                    . '::class,'
                    . PHP_EOL;
            }

            $this->markTestIncomplete($message);
        } else {
            $this->assertSame($expect, $actual);
            $this->assertSame($finalNesting, $parser->listNesting());
        }
    }

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
                $x = array(1, 2);
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'with-keys' => [
                <<<'CODE'
                <?php
                $x = array(1 => 'a', 2 => 'b');
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TStringLiteral::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'nested' => [
                <<<'CODE'
                <?php
                $x = array(array(1));
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TArrayOpeningBracket::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayClosingBracket::class,
                    TArrayClosingBracket::class,
                    TSemicolon::class,
                ],
            ],
            'typehint' => [
                <<<'CODE'
                <?php
                function foo(array $bar) {}
                CODE,
                [
                    TPhpOpeningTag::class,
                    TFunction::class,
                    TFunctionName::class,
                    TParamsOpeningParen::class,
                    TArray::class,
                    TVariable::class,
                    TParamsClosingParen::class,
                    TFunctionOpeningBrace::class,
                    TFunctionClosingBrace::class,
                ],
            ],
        ];
    }
}

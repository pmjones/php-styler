<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpStyler\TestFormat;
use PHPUnit\Framework\Attributes\DataProvider;

class TListAsArrayTest extends TTestCase
{
    #[DataProvider('provide')]
    public function test(
        string $code,
        array $expect,
        array $finalNesting = [],
        string $reporting = self::IGNORE_WHITESPACE,
    ) : void
    {
        $parser = new Parser(
            new TestFormat(parseAs: [TList::class => TListAsArray::class]),
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
                list($foo, $bar) = $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TVariable::class,
                    TArrayComma::class,
                    TVariable::class,
                    TArrayClosingBracket::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'with-keys' => [
                <<<'CODE'
                <?php
                list(0 => $foo, 1 => $bar) = $baz;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TVariable::class,
                    TArrayComma::class,
                    TIntegerLiteral::class,
                    TArrayDoubleArrow::class,
                    TVariable::class,
                    TArrayClosingBracket::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
            'nested' => [
                <<<'CODE'
                <?php
                list($a, list($b, $c)) = $d;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TArrayOpeningBracket::class,
                    TVariable::class,
                    TArrayComma::class,
                    TArrayOpeningBracket::class,
                    TVariable::class,
                    TArrayComma::class,
                    TVariable::class,
                    TArrayClosingBracket::class,
                    TArrayClosingBracket::class,
                    TAssign::class,
                    TVariable::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

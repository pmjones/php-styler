<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
abstract class TTestCase extends \PHPUnit\Framework\TestCase
{
    protected const IGNORE_WHITESPACE = 'IGNORE_WHITESPACE';

    protected const REPORT_WHITESPACE = 'REPORT_WHITESPACE';

    protected const REPORT_LINEBREAKS = 'REPORT_LINEBREAKS';

    protected const REPORT_SYNTHETIC = 'REPORT_SYNTHETIC';

    /**
     * @param array<int, class-string> $expect
     * @param array<int, class-string> $finalNesting
     * @dataProvider provide
     */
    public function test(
        string $code,
        array $expect,
        array $finalNesting = [],
        string $reporting = self::IGNORE_WHITESPACE,
    ) : void
    {
        $parser = new Parser();
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
                $message .= '                    ' . end($parts) . '::class,' . PHP_EOL;
            }

            $this->markTestIncomplete($message);
        } else {
            $this->assertSame($expect, $actual);
            $this->assertSame($finalNesting, $parser->listNesting());
        }
    }

    /**
     * @return array<string, array{0:string, 1:array<int, class-string>, 2?:array<int, class-string>}>
     */
    abstract public static function provide() : array;
}

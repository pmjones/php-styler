<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Rule\NormalizeTrailingCommas;
use PHPUnit\Framework\TestCase;

class ExamplesTest extends TestCase
{
    private Styler $styler;

    protected function setUp() : void
    {
        $this->styler = new Styler(eol: "\n", rules: [new NormalizeTrailingCommas()]);
    }

    /**
     * @dataProvider provideExample
     */
    public function testExample(string $sourceFile) : void
    {
        $source = (string) file_get_contents($sourceFile);
        $actual = ($this->styler)($source);
        $this->assertSame($source, $actual);
    }

    /** @return array<string, array{string}> */
    public static function provideExample() : array
    {
        $provide = [];
        $sourceFiles = glob(__DIR__ . '/Examples/*.php') ?: [];

        foreach ($sourceFiles as $sourceFile) {
            $key = ltrim((string) strrchr($sourceFile, '/'), '/');

            $provide[$key] = [$sourceFile];
        }

        return $provide;
    }
}

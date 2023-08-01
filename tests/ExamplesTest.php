<?php
declare(strict_types=1);

namespace PhpStyler;

class ExamplesTest extends TestCase
{
    /**
     * @dataProvider provideExample
     */
    public function testExample(string $sourceFile)
    {
        $source = file_get_contents($sourceFile);
        $this->assertPrint($source, $source);
    }

    public static function provideExample() : array
    {
        $provide = [];
        $sourceFiles = glob(__DIR__ . '/Examples/*.php');

        foreach ($sourceFiles as $sourceFile) {
            $key = ltrim(strrchr(str_replace('.source.php', '', $sourceFile), '/'), '/') ;
            $provide[$key] = [$sourceFile];
        }

        return $provide;
    }
}

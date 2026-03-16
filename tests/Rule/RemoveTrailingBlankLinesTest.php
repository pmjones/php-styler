<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PHPUnit\Framework\TestCase;
use PhpStyler\Styler;

class RemoveTrailingBlankLinesTest extends TestCase
{
    private function assertStyled(
        string $code,
        string $expect,
    ) : void
    {
        $styler = new Styler(
            eol: "\n",
            rules: [new RemoveTrailingBlankLines()],
        );
        $actual = $styler($code);
        $this->assertSame($expect, $actual);
    }

    /**
     * @dataProvider provide
     */
    public function test(string $code, string $expect) : void
    {
        $this->assertStyled($code, $expect);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'no-trailing-blank-lines' => [
                <<<'CODE'
                <?php
                $foo = 'bar';
                CODE,
                <<<'EXPECT'
                <?php
                $foo = 'bar';

                EXPECT,
            ],

            'trailing-blank-lines-removed' => [
                <<<'CODE'
                <?php
                $foo = 'bar';


                CODE,
                <<<'EXPECT'
                <?php
                $foo = 'bar';

                EXPECT,
            ],

            'only-opening-tag' => [
                <<<'CODE'
                <?php
                CODE,
                // parser renders <?php with trailing space
                "<?php \n",
            ],

        ];
    }
}

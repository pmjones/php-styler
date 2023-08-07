<?php
declare(strict_types=1);

namespace PhpStyler;

class StylerTest extends TestCase
{
    public function testStyle_empty() : void
    {
        $actual = $this->styler->style([]);
        $this->assertSame("<?php\n", $actual);
    }

    public function testInf() : void
    {
        $source = <<<'SOURCE'
        <?php
        $inf = 1e10000;
        $ninf = -1e10000;

        SOURCE;

        $expect = <<<'EXPECT'
        <?php
        $inf = \INF;
        $ninf = -\INF;

        EXPECT;

        $this->assertPrint($expect, $source);
    }
}

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
}

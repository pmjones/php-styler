<?php
declare(strict_types=1);

namespace PhpStyler;

class IssuesTest extends TestCase
{
    /**
     * https://github.com/pmjones/php-styler/issues/4
     */
    public function testIssue4() : void
    {
        $source = <<<'SOURCE'
        <?php
        if ($foo) {
            $foo = 'bar';
        } else if ($foo) {
            $foo = 'dib';
        }

        SOURCE;
        $expect = <<<'EXPECT'
        <?php
        if ($foo) {
            $foo = 'bar';
        } elseif ($foo) {
            $foo = 'dib';
        }

        EXPECT;
        $this->assertPrint($expect, $source);
    }
}

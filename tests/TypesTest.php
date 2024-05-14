<?php
declare(strict_types=1);

namespace PhpStyler;

class TypesTest extends TestCase
{
    public function testComplexTypes() : void
    {
        if (version_compare(PHP_VERSION, '8.2.0') < 0) {
            $this->markTestSkipped('Cannot test complex types before PHP 8.1');
        }

        $source = <<<'SOURCE'
        <?php
        function funcname(Foo $foo, Bar&Baz $baz, Zim|(Gir&Irk) $doom)
        {
        }

        SOURCE;

        $this->assertPrint($source, $source);
    }
}

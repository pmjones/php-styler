<?php
declare(strict_types=1);

namespace PhpStyler;

use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testFromParserCapturesDebugInfo() : void
    {
        $parser = new Parser();

        try {
            // Two return type colons trigger "Unknown kind of colon"
            $parser("<?php class Foo { public function bar() : never : int {} }");
            $this->fail('Expected Exception was not thrown');
        } catch (Exception $e) {
            // message is preserved
            $this->assertStringContainsString('Unknown kind of colon', $e->getMessage());
            $this->assertStringContainsString('on line 1', $e->getMessage());

            // debug info is populated
            $this->assertSame(1, $e->debug['line']);
            $this->assertIsInt($e->debug['pos']);
            $this->assertSame(':', $e->debug['currentTokenText']);
            $this->assertSame(':', $e->debug['currentTokenName']);
            $this->assertIsString($e->debug['recentSourceText']);
            $this->assertStringContainsString('never', $e->debug['recentSourceText']);
            $this->assertIsString($e->debug['upcomingSourceText']);
            $this->assertStringContainsString('int', $e->debug['upcomingSourceText']);
        }
    }

    public function testFromParserMultilineSource() : void
    {
        $parser = new Parser();
        $code = <<<'PHP'
        <?php
        class Foo
        {
            public function bar() : never : int
            {
            }
        }
        PHP;

        try {
            $parser($code);
            $this->fail('Expected Exception was not thrown');
        } catch (Exception $e) {
            $this->assertSame(4, $e->debug['line']);
            $this->assertSame(':', $e->debug['currentTokenText']);
            $this->assertIsString($e->debug['recentSourceText']);
            $this->assertStringContainsString('never', $e->debug['recentSourceText']);
        }
    }

    public function testPlainExceptionHasEmptyDebug() : void
    {
        $e = new Exception('plain error');
        $this->assertSame([], $e->debug);
        $this->assertSame('plain error', $e->getMessage());
    }
}

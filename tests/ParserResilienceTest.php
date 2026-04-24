<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;
use PHPUnit\Framework\TestCase;

/**
 * Exercises defensive branches that handle syntactically invalid PHP
 * by reaching through the full styler pipeline.
 *
 * The styler is not a syntax validator — it operates on tokens, so
 * malformed input that PHP itself would reject at the AST level can
 * reach deep into the parser's invariant checks. These tests pin the
 * "we fail loud with a diagnosable error" contract for such inputs.
 */
class ParserResilienceTest extends TestCase
{
    public function testSwitchWithoutBraceThrowsDiagnosably() : void
    {
        $styler = new Styler(new DeclarationFormat());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown closing braceless');

        $styler("<?php\nswitch (\$x) echo 1;\n");
    }

    public function testMatchWithoutBraceThrowsDiagnosably() : void
    {
        $styler = new Styler(new DeclarationFormat());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown kind of closing braceless');

        $styler("<?php\n\$y = match (\$x) 1;\n");
    }
}

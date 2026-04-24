<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TSpace;
use PHPUnit\Framework\TestCase;

class LineTest extends TestCase
{
    private function line(string $code) : Line
    {
        $styler = new Styler(new DeclarationFormat());
        $tokens = $styler->parse($code);
        $lines = $styler->assemble($tokens);

        foreach ($lines as $line) {
            if (! $line->isBlank()) {
                return $line;
            }
        }

        return $lines[0];
    }

    public function testFindBestPairReturnsNullWhenNoPairs() : void
    {
        $line = $this->line("<?php\n\$x = 1;\n");
        $this->assertNull($line->findBestPair());
    }

    public function testFirstContentTokenReturnsNullForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertNull($line->firstContentToken());
    }

    public function testLastContentTokenReturnsNullForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertNull($line->lastContentToken());
    }

    public function testLastContentIndexReturnsZeroForWhitespaceOnlyLine() : void
    {
        $line = new Line([new TSpace(AToken::SYNTHETIC, ' ')], 0, '', 0);
        $this->assertSame(0, $line->lastContentIndex());
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Format\PlainFormat;
use PhpStyler\Parser;
use PhpToken;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the defensive `default =>` throw arm in
 * TContinuationBrace::parse(). That arm is unreachable through any
 * well-formed PHP source because the parser's invariants guarantee a
 * `}` continuation (`} else`, `} catch`, `} finally`, `} while`) only
 * arises when the current nesting is one of TIf / TElseif / TTry /
 * TCatch / TDo.
 *
 * This test manufactures a pathological nesting stack to validate that
 * an invariant violation fails loud with a diagnosable exception.
 */
class TContinuationBraceDefensiveTest extends TestCase
{
    public function testThrowsWhenUnderlyingNestingIsUnexpected() : void
    {
        $parser = new Parser(new PlainFormat());
        $parser("<?php ");

        // TContinuationBrace::parse inspects getNesting() directly without
        // any preceding add/pop, so only the top of the stack needs to be
        // pathological.
        $parser->nestingStack->push(new TClass(T_CLASS, 'class', 1, 0));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown kind of parting brace');

        TContinuationBrace::parse($parser, new PhpToken(ord('}'), '}', 1, 0));
    }
}

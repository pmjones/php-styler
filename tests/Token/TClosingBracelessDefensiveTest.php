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
 * TClosingBraceless::parse(). That arm is unreachable through any
 * well-formed PHP source because the parser's invariants guarantee a
 * TOpeningBraceless only sits directly on top of one of six control
 * nestings (TIf / TElse / TElseif / TWhile / TFor / TForeach).
 *
 * This test manufactures a pathological nesting stack to validate that
 * an invariant violation fails loud with a diagnosable exception rather
 * than a silent error.
 */
class TClosingBracelessDefensiveTest extends TestCase
{
    public function testThrowsWhenUnderlyingNestingIsUnexpected() : void
    {
        $parser = new Parser(new PlainFormat());

        // Prime all private parser state (parsed, source, split trackers,
        // nestingStack) by running a minimal invocation, then rewind source
        // so the in-progress parse has valid lookahead tokens.
        $parser("<?php ;");
        $parser->source->setOffset(0);

        // Manually construct the pathological stack: TOpeningBraceless on
        // top of a nesting class that isn't one of the six accepted
        // controls. This cannot occur in valid parser flow because
        // TOpeningBraceless::parse only pushes when the underlying nesting
        // is one of TIf / TElse / TElseif / TWhile / TFor / TForeach.
        $parser->nestingStack->push(new TClass(T_CLASS, 'class', 1, 0));
        $parser->nestingStack->push(new TOpeningBraceless(T_WHITESPACE, '', 1, 0));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown kind of closing braceless');

        TClosingBraceless::parse($parser, new PhpToken(ord(';'), ';', 1, 0));
    }
}

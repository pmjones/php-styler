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
 * TContinuationBraceless::parse(). That arm is unreachable through any
 * well-formed PHP source because a braceless-body continuation
 * (`else` / `elseif` after a braceless `if` / `elseif` body) only
 * arises when the underlying nesting is TIf or TElseif.
 *
 * This test manufactures a pathological nesting stack to validate that
 * an invariant violation fails loud with a diagnosable exception.
 */
class TContinuationBracelessDefensiveTest extends TestCase
{
    public function testThrowsWhenUnderlyingNestingIsUnexpected() : void
    {
        $parser = new Parser(new PlainFormat());

        // Prime parser state, then rewind source so add()/lineBreak() have
        // valid lookahead tokens during the in-progress parse.
        $parser("<?php ;");
        $parser->source->setOffset(0);

        // TContinuationBraceless::parse adds a synthetic TSemicolon, pops a
        // TOpeningBraceless, then inspects getNesting(). Push a nesting
        // class that isn't TIf or TElseif underneath TOpeningBraceless.
        $parser->nestingStack->push(new TClass(T_CLASS, 'class', 1, 0));
        $parser->nestingStack->push(new TOpeningBraceless(T_WHITESPACE, '', 1, 0));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown kind of parting braceless');

        TContinuationBraceless::parse($parser, new PhpToken(T_ELSE, 'else', 1, 0));
    }
}

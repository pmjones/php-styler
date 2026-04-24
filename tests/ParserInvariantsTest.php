<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\PlainFormat;
use PhpStyler\Token;
use PHPUnit\Framework\TestCase;

/**
 * Exercises parser invariant-enforcement paths that are unreachable via
 * any accepted PHP input. These tests manufacture pathological parser
 * state to validate that invariant violations fail loud with a
 * diagnosable exception rather than silently producing wrong output.
 */
class ParserInvariantsTest extends TestCase
{
    public function testPopNestingThrowsWhenStackTopDoesNotMatchExpected() : void
    {
        $parser = new Parser(new PlainFormat());
        $parser("<?php ;");
        $parser->source->setOffset(0);

        // Push a TClass nesting, then try to pop expecting TFunction:
        // mismatch should throw diagnosably.
        $parser->nestingStack->push(
            new Token\TClass(T_CLASS, 'class', 1, 0),
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Expected to pop PhpStyler\Token\TFunction, got PhpStyler\Token\TClass instead',
        );

        $parser->popNesting(Token\TFunction::class);
    }

    public function testPopNestingThrowsWhenStackIsEmpty() : void
    {
        $parser = new Parser(new PlainFormat());
        $parser("<?php ;");
        $parser->source->setOffset(0);

        // Empty stack — popping anything should throw with an empty
        // `actual` segment in the message.
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Expected to pop PhpStyler\Token\TClass, got  instead',
        );

        $parser->popNesting(Token\TClass::class);
    }

    public function testSpaceReturnsEarlyWhenParsedIsEmpty() : void
    {
        // space() defends against being called on a parser with no
        // already-parsed tokens. A fresh parser has none, so calling
        // space() before __invoke hits the early-return branch. The
        // invariant being pinned is "no exception is thrown."
        $parser = new Parser(new PlainFormat());

        $this->expectNotToPerformAssertions();
        $parser->space();
    }
}

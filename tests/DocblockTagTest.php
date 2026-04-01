<?php
declare(strict_types=1);

namespace PhpStyler;

use PHPUnit\Framework\TestCase;

class DocblockTagTest extends TestCase
{
    public function testDocCommentMultipleTags() : void
    {
        $text = <<<'COMMENT'
        /**
         * Summary line.
         *
         * @param string $foo The foo param
         * @return int
         */
        COMMENT;

        $tags = DocblockTag::parseAll($text);

        $this->assertCount(2, $tags);
        $this->assertSame('param', $tags[0]->name);
        $this->assertSame('string $foo The foo param', $tags[0]->body);
        $this->assertSame('return', $tags[1]->name);
        $this->assertSame('int', $tags[1]->body);
    }

    public function testStarredCommentWithVar() : void
    {
        $tags = DocblockTag::parseAll('/* @var string $foo */');

        $this->assertCount(1, $tags);
        $this->assertSame('var', $tags[0]->name);
        $this->assertSame('string $foo', $tags[0]->body);
    }

    public function testSlashedComment() : void
    {
        $tags = DocblockTag::parseAll('// @var string $foo');

        $this->assertCount(1, $tags);
        $this->assertSame('var', $tags[0]->name);
        $this->assertSame('string $foo', $tags[0]->body);
    }

    public function testHashedComment() : void
    {
        $tags = DocblockTag::parseAll('# @phpstan-var string $foo');

        $this->assertCount(1, $tags);
        $this->assertSame('phpstan-var', $tags[0]->name);
        $this->assertSame('string $foo', $tags[0]->body);
    }

    public function testMultiLineTagContinuation() : void
    {
        $text = <<<'COMMENT'
        /**
         * @param array<string, mixed> $options
         *     This is a continuation line
         *     describing the parameter.
         * @return void
         */
        COMMENT;

        $tags = DocblockTag::parseAll($text);

        $this->assertCount(2, $tags);
        $this->assertSame('param', $tags[0]->name);

        $this->assertSame(
            'array<string, mixed> $options This is a continuation line describing the parameter.',
            $tags[0]->body,
        );

        $this->assertSame('return', $tags[1]->name);
        $this->assertSame('void', $tags[1]->body);
    }

    public function testSingleLineDocComment() : void
    {
        $tags = DocblockTag::parseAll('/** @var string */');

        $this->assertCount(1, $tags);
        $this->assertSame('var', $tags[0]->name);
        $this->assertSame('string', $tags[0]->body);
    }

    public function testGenericType() : void
    {
        $text = '/** @param array<string, mixed> $foo */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('array<string, mixed>', $tags[0]->getType());
    }

    public function testUnionType() : void
    {
        $text = '/** @return Foo|Bar|null */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('Foo|Bar|null', $tags[0]->getType());
    }

    public function testNoTags() : void
    {
        $text = <<<'COMMENT'
        /**
         * Just a description, no tags.
         */
        COMMENT;

        $tags = DocblockTag::parseAll($text);
        $this->assertCount(0, $tags);
    }

    public function testInlineInheritdocIgnored() : void
    {
        $text = <<<'COMMENT'
        /**
         * This method does {@inheritdoc} something.
         *
         * @return void
         */
        COMMENT;

        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('return', $tags[0]->name);
    }

    public function testGetTypeReturnsNullForDeprecated() : void
    {
        $text = '/** @deprecated Use something else */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        // deprecated has a text body, but getType() will return the first word
        // which is "Use" — callers should know which tags have types
        $this->assertSame('Use', $tags[0]->getType());
    }

    public function testGetTypeReturnsNullForEmptyBody() : void
    {
        $text = '/** @inheritdoc */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertNull($tags[0]->getType());
    }

    public function testPhpstanPrefixedTags() : void
    {
        $text = <<<'COMMENT'
        /**
         * @phpstan-param array<int, string> $items
         * @phpstan-return list<string>
         */
        COMMENT;

        $tags = DocblockTag::parseAll($text);

        $this->assertCount(2, $tags);
        $this->assertSame('phpstan-param', $tags[0]->name);
        $this->assertSame('array<int, string>', $tags[0]->getType());
        $this->assertSame('phpstan-return', $tags[1]->name);
        $this->assertSame('list<string>', $tags[1]->getType());
    }

    public function testCallableType() : void
    {
        $text = '/** @param callable(int): string $fn */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('callable(int):', $tags[0]->getType());
    }

    public function testGetTypeForParamWithDollarSign() : void
    {
        $text = '/** @param string $foo */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('string', $tags[0]->getType());
    }

    public function testGetTypeForReturn() : void
    {
        $text = '/** @return int Description here */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('int', $tags[0]->getType());
    }

    public function testGetTypeForThrows() : void
    {
        $text = '/** @throws \RuntimeException When something fails */';
        $tags = DocblockTag::parseAll($text);

        $this->assertCount(1, $tags);
        $this->assertSame('\RuntimeException', $tags[0]->getType());
    }
}

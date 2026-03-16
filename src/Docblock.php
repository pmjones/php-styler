<?php
declare(strict_types=1);

namespace PhpStyler;

class Docblock
{
    /**
     * @param list<DocblockTag> $tags
     */
    public function __construct(
        public readonly string $text,
        public readonly array $tags,
    ) {
    }

    public static function parse(string $comment) : self
    {
        $comment = trim($comment);

        if (str_starts_with($comment, '/**')) {
            $body = substr($comment, 3);
            $body = preg_replace('/\*\/\s*$/', '', $body);
        } elseif (str_starts_with($comment, '/*')) {
            $body = substr($comment, 2);
            $body = preg_replace('/\*\/\s*$/', '', $body);
        } elseif (str_starts_with($comment, '//')) {
            $body = substr($comment, 2);
        } elseif (str_starts_with($comment, '#')) {
            $body = substr($comment, 1);
        } else {
            $body = $comment;
        }

        /** @var string $body */
        $lines = explode("\n", $body);

        foreach ($lines as &$line) {
            $line = preg_replace('/^\s*\*?\s?/', '', $line);
        }

        unset($line);

        /** @var list<string> $lines */
        $textLines = [];
        $tagLines = [];
        $inTags = false;

        foreach ($lines as $line) {
            if (! $inTags && preg_match('/^@[\w-]+/', $line)) {
                $inTags = true;
            }

            if ($inTags) {
                $tagLines[] = $line;
            } else {
                $textLines[] = $line;
            }
        }

        $text = trim(implode("\n", $textLines));
        $tags = DocblockTag::parseAll($comment);

        return new self($text, $tags);
    }
}

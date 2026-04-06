<?php
declare(strict_types=1);

namespace PhpStyler;

class DocblockTag
{
    /**
     * @return list<self>
     */
    public static function parseAll(string $text) : array
    {
        $text = trim($text);

        if (str_starts_with($text, '/**')) {
            $text = substr($text, 3);
            $text = preg_replace('/\*\/\s*$/', '', $text);
        } elseif (str_starts_with($text, '/*')) {
            $text = substr($text, 2);
            $text = preg_replace('/\*\/\s*$/', '', $text);
        } elseif (str_starts_with($text, '//')) {
            $text = substr($text, 2);
        } elseif (str_starts_with($text, '#')) {
            $text = substr($text, 1);
        }

        /** @var string $text */
        $lines = explode("\n", $text);

        foreach ($lines as &$line) {
            $line = preg_replace('/^\s*\*?\s?/', '', $line);
        }

        unset($line);

        /** @var list<string> $lines */
        $tags = [];
        $currentName = null;
        $currentBody = null;

        foreach ($lines as $line) {
            if (preg_match('/^@([\w-]+)\s*(.*)$/s', $line, $matches)) {
                if ($currentName !== null) {
                    $tags[] = new self($currentName, trim((string) $currentBody));
                }

                $currentName = $matches[1];
                $currentBody = $matches[2];
            } elseif ($currentName !== null && $line !== '') {
                $currentBody .= ' ' . ltrim($line);
            }
        }

        if ($currentName !== null) {
            $tags[] = new self($currentName, trim((string) $currentBody));
        }

        return $tags;
    }

    public function __construct(
        public readonly string $name,
        public readonly string $body,
    ) {
    }

    public function getType() : ?string
    {
        if ($this->body === '') {
            return null;
        }

        $body = $this->body;
        $len = strlen($body);
        $depth = 0;
        $i = 0;

        while ($i < $len) {
            $char = $body[$i];

            if ($char === '<' || $char === '(' || $char === '{') {
                $depth ++;
                $i ++;
                continue;
            }

            if ($char === '>' || $char === ')' || $char === '}') {
                $depth --;
                $i ++;
                continue;
            }

            if ($depth === 0) {
                if ($char === '$') {
                    $type = rtrim(substr($body, 0, $i));

                    return $type === '' ? null : $type;
                }

                if ($char === ' ' || $char === "\t") {
                    $type = substr($body, 0, $i);

                    return $type === '' ? null : $type;
                }
            }

            $i ++;
        }

        // Entire body is the type (e.g. @return string)
        return $body;
    }
}

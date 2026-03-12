<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

class TCommentStarred extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->hasPrevLineBreak() || ! $parser->hasNextEol()) {
            $parser->add($source, TCommentStarredInline::class);
            $parser->space();
            return;
        }

        if (strpos($source->text, PHP_EOL) === false) {
            $parser->parse($source, TCommentStarredOneline::class);
            return;
        }

        $parser->add($source, self::class);
    }

    public function render(Line $line) : string
    {
        $indent = str_repeat($line->indentStr, $line->indent);
        $lines = explode("\n", $this->text);

        foreach ($lines as $i => &$l) {
            if ($i > 0) {
                $l = $indent . ' ' . ltrim($l);
            }
        }

        return implode("\n", $lines);
    }
}

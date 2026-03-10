<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

class TCommentStarred extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if (! $parser->hasPrevSourceNewline() || ! $parser->hasNextEol()) {
            $token = $parser->add($unparsed, TCommentStarredInline::class);
            $parser->space();
            $parser->transferLineBreakAfter($token);
            return;
        }

        if (strpos($unparsed->text, PHP_EOL) === false) {
            $parser->parse($unparsed, TCommentStarredOneline::class);
            return;
        }

        $parser->add($unparsed, self::class);
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

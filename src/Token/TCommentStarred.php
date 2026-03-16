<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

class TCommentStarred extends T implements TDocblock
{
    protected ?Docblock $docblock = null;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->hasPrevLineBreak() || ! $parser->hasNextEol()) {
            $parser->add($source, TCommentStarredMidStatement::class);
            return;
        }

        $parser->add($source, self::class);
    }

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
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

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;
use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DOC_COMMENT
 *
 * Syntax: /**
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.comments.php PHPDoc style comments
 */
class TDocComment extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->hasPrevLineBreak() || ! $parser->hasNextEol()) {
            $parser->add($source, TDocCommentMidStatement::class);
            $parser->space();
            return;
        }

        if (strpos($source->text, PHP_EOL) === false) {
            $parser->parse($source, TDocCommentOneline::class);
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

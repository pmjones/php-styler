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
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if (! $parser->hasPrevSourceNewline() || ! $parser->hasNextEol()) {
            $token = $parser->add($unparsed, TDocCommentInline::class);
            $parser->space();
            $parser->transferLineBreakAfter($token);
            return;
        }

        if (strpos($unparsed->text, PHP_EOL) === false) {
            $parser->parse($unparsed, TDocCommentOneline::class);
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

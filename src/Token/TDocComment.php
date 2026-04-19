<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
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
class TDocComment extends AToken implements ADocblock
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            (
                ! str_contains($source->text, "\n")
                && $parser->hasPrev(ASplittableComma::class)
            )
            || ! $parser->hasPrevLineBreak()
            || ! $parser->source->hasNextEol()
        ) {
            $parser->add($source, TDocCommentMidStatement::class);
            return;
        }

        $parser->add($source, self::class);
    }

    protected ?Docblock $docblock = null;

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

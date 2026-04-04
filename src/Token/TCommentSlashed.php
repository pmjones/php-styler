<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
use PhpStyler\Parser;
use PhpToken;

class TCommentSlashed extends AToken implements ADocblock
{
    protected ?Docblock $docblock = null;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->add($source, TCommentSlashed::class);
            return;
        }

        $parser->add($source, TCommentSlashedMidStatement::class);
    }

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
    }
}

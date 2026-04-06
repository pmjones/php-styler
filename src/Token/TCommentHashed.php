<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
use PhpStyler\Parser;
use PhpToken;

class TCommentHashed extends AToken implements ADocblock
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->add($source, TCommentHashed::class);
            return;
        }

        $parser->add($source, TCommentHashedMidStatement::class);
    }

    protected ?Docblock $docblock = null;

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
    }
}

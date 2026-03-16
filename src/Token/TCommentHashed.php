<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;
use PhpStyler\Parser;
use PhpToken;

class TCommentHashed extends T implements TDocblock
{
    protected ?Docblock $docblock = null;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->add($source, TCommentHashed::class);
            return;
        }

        $parser->add($source, TCommentHashedMidStatement::class);
    }

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
    }
}

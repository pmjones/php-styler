<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TPhpOpeningTag;
use PhpStyler\Token\TPhpOpeningTagInline;
use PhpStyler\Token\TSpace;

class EnsurePhpOpeningTagNewline implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        if ($tokens === []) {
            return $tokens;
        }

        if (! ($tokens[0] instanceof TPhpOpeningTagInline)) {
            return $tokens;
        }

        $old = $tokens[0];

        $result = [];
        $result[] = new TPhpOpeningTag(
            $old->id,
            $old->text,
            $old->line,
            $old->pos,
        );
        $result[] = new TLineBreak(T::SYNTHETIC, '', $old->line, $old->pos);

        // skip the inline tag and any following TSpace
        $i = 1;

        if (isset($tokens[$i]) && $tokens[$i] instanceof TSpace) {
            $i ++;
        }

        // append the rest
        for (; $i < count($tokens); $i ++) {
            $result[] = $tokens[$i];
        }

        return $result;
    }
}

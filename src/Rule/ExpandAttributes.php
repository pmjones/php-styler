<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAttribute;
use PhpStyler\Token\TAttributeClosingBracket;
use PhpStyler\Token\TAttributeComma;
use PhpStyler\Token\TLineBreak;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSplitComma;

class ExpandAttributes implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! ($token instanceof TAttributeComma)) {
                $result[] = $token;
                continue;
            }

            // skip the TSplitComma that follows TAttributeComma
            if (isset($tokens[$i + 1]) && $tokens[$i + 1] instanceof TSplitComma) {
                $i ++;
            }

            // skip any TSpace after that
            if (isset($tokens[$i + 1]) && $tokens[$i + 1] instanceof TSpace) {
                $i ++;
            }

            // replace comma with: ] + line break + #[
            $result[] = new TAttributeClosingBracket(AToken::SYNTHETIC, ']');
            $result[] = new TLineBreak(AToken::SYNTHETIC, '');
            $result[] = new TAttribute(AToken::SYNTHETIC, '#[');
        }

        return $result;
    }
}

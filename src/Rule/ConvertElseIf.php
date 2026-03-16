<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\TElse;
use PhpStyler\Token\TElseif;
use PhpStyler\Token\TIf;
use PhpStyler\Token\TSpace;

class ConvertElseIf implements TokenRule
{
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (
                $token instanceof TElse
                && isset($tokens[$i + 1])
                && $tokens[$i + 1] instanceof TSpace
                && isset($tokens[$i + 2])
                && $tokens[$i + 2] instanceof TIf
            ) {
                $if = $tokens[$i + 2];
                $result[] = new TElseif(T_ELSEIF, 'elseif', $if->line, $if->pos);
                $i += 2;
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TStringLiteral;

class ConvertDoubleToSingleQuote implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];

        foreach ($tokens as $token) {
            if ($token instanceof TStringLiteral && $token->text[0] === '"') {
                $inner = substr($token->text, 1, -1);

                if (str_contains($inner, "'")) {
                    $result[] = $token;
                    continue;
                }

                if ($this->hasUnsafeEscape($inner)) {
                    $result[] = $token;
                    continue;
                }

                $converted = str_replace('\\"', '"', $inner);
                $newText = "'" . $converted . "'";
                $result[] = new TStringLiteral(
                    $token->id,
                    $newText,
                    $token->line,
                    $token->pos,
                );
                continue;
            }

            $result[] = $token;
        }

        return $result;
    }

    private function hasUnsafeEscape(string $inner) : bool
    {
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i ++) {
            if ($inner[$i] === '\\') {
                if ($i + 1 < $len) {
                    $next = $inner[$i + 1];

                    if ($next !== '\\' && $next !== '"') {
                        return true;
                    }

                    $i ++;
                }
            }
        }

        return false;
    }
}

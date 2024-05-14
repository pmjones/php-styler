<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpParser\ErrorHandler;
use PhpParser\Node\Stmt;
use PhpParser\Parser\Php7;
use PhpToken;

class Parser extends Php7
{
    /**
     * @return Stmt[]|null
     */
    public function parse(string $code, ?ErrorHandler $errorHandler = null)
    {
        $code = $this->convertElseIf($code);
        return parent::parse($code, $errorHandler);
    }

    /**
     * Converts `else if` to `elseif` directly in the original code.
     */
    protected function convertElseIf(string $original) : string
    {
        $converted = '';
        $tokens = PhpToken::tokenize($original, TOKEN_PARSE);
        $k = count($tokens);

        for ($i = 0; $i < $k; $i ++) {
            $token = $tokens[$i];
            $plus1 = $tokens[$i + 1] ?? null;
            $plus2 = $tokens[$i + 2] ?? null;

            if (
                $token->id === T_ELSE
                && $plus1?->id === T_WHITESPACE
                && $plus2?->id === T_IF
            ) {
                $converted .= 'elseif';
                $i += 2;
                continue;
            }

            $converted .= $token;
        }

        return $converted;
    }
}

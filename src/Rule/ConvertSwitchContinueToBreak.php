<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBreak;
use PhpStyler\Token\TContinue;
use PhpStyler\Token\TDoContinuationBrace;
use PhpStyler\Token\TDoOpeningBrace;
use PhpStyler\Token\TForClosingBrace;
use PhpStyler\Token\TForeachClosingBrace;
use PhpStyler\Token\TForeachOpeningBrace;
use PhpStyler\Token\TForOpeningBrace;
use PhpStyler\Token\TIntegerLiteral;
use PhpStyler\Token\TSpace;
use PhpStyler\Token\TSwitchClosingBrace;
use PhpStyler\Token\TSwitchOpeningBrace;
use PhpStyler\Token\TWhileClosingBrace;
use PhpStyler\Token\TWhileOpeningBrace;

class ConvertSwitchContinueToBreak implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        /** @var list<'switch'|'loop'> */
        $stack = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token instanceof TSwitchOpeningBrace) {
                $stack[] = 'switch';
                $result[] = $token;
                continue;
            }

            if ($token instanceof TSwitchClosingBrace) {
                array_pop($stack);
                $result[] = $token;
                continue;
            }

            if (
                $token instanceof TForOpeningBrace
                || $token instanceof TForeachOpeningBrace
                || $token instanceof TWhileOpeningBrace
                || $token instanceof TDoOpeningBrace
            ) {
                $stack[] = 'loop';
                $result[] = $token;
                continue;
            }

            if (
                $token instanceof TForClosingBrace
                || $token instanceof TForeachClosingBrace
                || $token instanceof TWhileClosingBrace
                || $token instanceof TDoContinuationBrace
            ) {
                array_pop($stack);
                $result[] = $token;
                continue;
            }

            if ($token instanceof TContinue) {
                $top = $stack === [] ? null : $stack[count($stack) - 1];

                if ($top === 'switch') {
                    // check for optional integer literal
                    $j = $i + 1;

                    if ($j < $count && $tokens[$j] instanceof TSpace) {
                        $j ++;
                    }

                    if (
                        $j < $count
                        && $tokens[$j] instanceof TIntegerLiteral
                        && (int) $tokens[$j]->text > 1
                    ) {
                        $result[] = $token;
                        continue;
                    }

                    $result[] = new TBreak(
                        T_BREAK,
                        'break',
                        $token->line,
                        $token->pos,
                    );
                    continue;
                }
            }

            $result[] = $token;
        }

        return $result;
    }
}

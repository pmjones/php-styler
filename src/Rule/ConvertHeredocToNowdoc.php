<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TCurlyOpen;
use PhpStyler\Token\TEncapsedVariable;
use PhpStyler\Token\THeredocEnd;
use PhpStyler\Token\THeredocStart;

class ConvertHeredocToNowdoc implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! $token instanceof THeredocStart) {
                continue;
            }

            // already a nowdoc? (label is single-quoted)
            if (str_contains($token->text, "'")) {
                continue;
            }

            // scan forward to find the matching THeredocEnd
            $hasInterpolation = false;

            for ($j = $i + 1; $j < $count; $j ++) {
                if ($tokens[$j] instanceof THeredocEnd) {
                    break;
                }

                if (
                    $tokens[$j] instanceof TEncapsedVariable
                    || $tokens[$j] instanceof TCurlyOpen
                ) {
                    $hasInterpolation = true;
                    break;
                }
            }

            if ($hasInterpolation) {
                continue;
            }

            // convert <<<LABEL\n to <<<'LABEL'\n
            $text = $token->text;

            if (preg_match('/^(<<<\s*)(\w+)(\s*)$/', $text, $matches)) {
                $newText = $matches[1] . "'" . $matches[2] . "'" . $matches[3];
                $tokens[$i] = new THeredocStart(
                    $token->id,
                    $newText,
                    $token->line,
                    $token->pos,
                );
            }
        }

        return $tokens;
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Line;
use PhpStyler\Token\TFunctionOpeningBrace;
use PhpStyler\Token\TSpace;

class RejoinOrphans implements LineRule
{
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function apply(array $lines) : array
    {
        for ($i = 0; $i < count($lines) - 1; $i ++) {
            $line = $lines[$i];
            $nextLine = $lines[$i + 1];

            if (
                ! $line->isBlank()
                && $line->contentTokenCount() === 1
                && $nextLine->firstContentToken() instanceof TFunctionOpeningBrace
            ) {
                $merged = array_merge(
                    $line->getTokens(),
                    [new TSpace(T_WHITESPACE, ' ')],
                    $nextLine->getTokens(),
                );

                $lines[
                    $i
                ] = new Line(
                    $merged,
                    $line->indent,
                    $line->indentStr,
                    $line->indentLen,
                );

                array_splice($lines, $i + 1, 1);
                continue;
            }
        }

        return array_values($lines);
    }
}

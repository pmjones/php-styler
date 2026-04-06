<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Token\TAnonymousClosingBrace;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TClasslikeClosingBrace;
use PhpStyler\Token\TClasslikeOpeningBrace;

abstract class AMemberNormalizer extends ALineRule
{
    /**
     * @param Line[] $lines
     * @return array<int, array{openIndex: int, closeIndex: int, memberIndent: int}>
     */
    protected function findClassBodyRegions(array $lines) : array
    {
        $stack = [];
        $regions = [];

        foreach ($lines as $i => $line) {
            $last = $line->lastContentToken();

            if (
                $last instanceof TClasslikeOpeningBrace
                || $last instanceof TAnonymousOpeningBrace
            ) {
                $stack[] = ['openIndex' => $i, 'indent' => $line->indent];
            }

            $first = $line->firstContentToken();

            if (
                (
                    $first instanceof TClasslikeClosingBrace
                    || $first instanceof TAnonymousClosingBrace
                )
                && $stack !== []
            ) {
                $opener = array_pop($stack);

                if ($opener['indent'] === $line->indent) {
                    $regions[] = [
                        'openIndex' => $opener['openIndex'],
                        'closeIndex' => $i,
                        'memberIndent' => $opener['indent'] + 1,
                    ];
                }
            }
        }

        return $regions;
    }
}

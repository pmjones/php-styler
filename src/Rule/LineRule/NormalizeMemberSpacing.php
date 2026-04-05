<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TAbstractMagicMethodEndSemicolon;
use PhpStyler\Token\TAbstractMethodEndSemicolon;
use PhpStyler\Token\TAnonymousClosingBrace;
use PhpStyler\Token\TAnonymousOpeningBrace;
use PhpStyler\Token\TBlankLine;
use PhpStyler\Token\TClasslikeClosingBrace;
use PhpStyler\Token\TClasslikeOpeningBrace;
use PhpStyler\Token\TConstEndSemicolon;
use PhpStyler\Token\TEnumCaseEndSemicolon;
use PhpStyler\Token\TFunctionClosingBrace;
use PhpStyler\Token\TMagicMethodClosingBrace;
use PhpStyler\Token\TPropertyEndSemicolon;
use PhpStyler\Token\TPropertyHooksAbstractClosingBrace;
use PhpStyler\Token\TPropertyHooksClosingBrace;
use PhpStyler\Token\TUseTraitClosingBrace;
use PhpStyler\Token\TUseTraitEndSemicolon;

class NormalizeMemberSpacing extends ALineRule
{
    public function __construct(
        private int $betweenConstants = 0,
        private int $betweenProperties = 0,
        private int $betweenEnumCases = 0,
        private int $betweenTraitUses = 0,
        private int $betweenMethods = 1,
        private int $betweenMagicMethods = 1,
    ) {
    }

    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function apply(array $lines) : array
    {
        $regions = $this->findClassBodyRegions($lines);

        usort($regions, fn ($a, $b) => $b['openIndex'] <=> $a['openIndex']);

        foreach ($regions as $region) {
            $lines = $this->adjustRegion($lines, $region);
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @return array<int, array{openIndex: int, closeIndex: int, memberIndent: int}>
     */
    private function findClassBodyRegions(array $lines) : array
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

    /**
     * @param Line[] $lines
     * @param array{openIndex: int, closeIndex: int, memberIndent: int} $region
     * @return Line[]
     */
    private function adjustRegion(array $lines, array $region) : array
    {
        $regionStart = $region['openIndex'] + 1;
        $regionEnd = $region['closeIndex'] - 1;
        $memberIndent = $region['memberIndent'];

        if ($regionStart > $regionEnd) {
            return $lines;
        }

        // Find member-ending line indices and types
        $memberEndings = [];

        for ($i = $regionStart; $i <= $regionEnd; $i ++) {
            $line = $lines[$i];

            if ($line->indent !== $memberIndent || $line->isBlank()) {
                continue;
            }

            $type = $this->memberEndType($line->lastContentToken());

            if ($type !== null) {
                $memberEndings[] = ['index' => $i, 'type' => $type];
            }
        }

        if (count($memberEndings) <= 1) {
            return $lines;
        }

        // Process consecutive pairs back-to-front
        for ($idx = count($memberEndings) - 1; $idx > 0; $idx --) {
            $current = $memberEndings[$idx - 1];
            $next = $memberEndings[$idx];

            if ($current['type'] !== $next['type']) {
                continue;
            }

            $desired = $this->desiredBlanks($current['type']);

            // Count actual blank lines between the two member endings
            $actual = 0;
            $blankIndices = [];

            for ($i = $current['index'] + 1; $i < $next['index']; $i ++) {
                if ($lines[$i]->isBlank()) {
                    $actual ++;
                    $blankIndices[] = $i;
                }
            }

            if ($actual === $desired) {
                continue;
            }

            if ($actual > $desired) {
                // Remove excess blank lines (from the end to preserve indices)
                $toRemove = $actual - $desired;

                for (
                    $r = count($blankIndices) - 1;
                    $r >= 0 && $toRemove > 0;
                    $r --, $toRemove --
                ) {
                    array_splice($lines, $blankIndices[$r], 1);
                }
            } elseif ($actual < $desired) {
                // Insert blank lines after the first member's ending line
                $insertAt = $current['index'] + 1;
                $refLine = $lines[$regionStart];

                $blanks = [];

                for ($b = 0; $b < $desired - $actual; $b ++) {
                    $blanks[] = new Line(
                        [new TBlankLine(AToken::SYNTHETIC, '')],
                        0,
                        $refLine->indentStr,
                        $refLine->indentLen,
                    );
                }

                array_splice($lines, $insertAt, 0, $blanks);
            }
        }

        return $lines;
    }

    private function desiredBlanks(string $type) : int
    {
        return match ($type) {
            'const' => $this->betweenConstants,
            'property' => $this->betweenProperties,
            'enumcase' => $this->betweenEnumCases,
            'traituse' => $this->betweenTraitUses,
            'magic' => $this->betweenMagicMethods,
            'method' => $this->betweenMethods,
            default => 1,
        };
    }

    private function memberEndType(?AToken $token) : ?string
    {
        if ($token === null) {
            return null;
        }

        return match (true) {
            $token instanceof TConstEndSemicolon => 'const',

            $token instanceof TPropertyEndSemicolon,
            $token instanceof TPropertyHooksClosingBrace,
            $token instanceof TPropertyHooksAbstractClosingBrace => 'property',

            $token instanceof TEnumCaseEndSemicolon => 'enumcase',

            $token instanceof TUseTraitEndSemicolon,
            $token instanceof TUseTraitClosingBrace => 'traituse',

            $token instanceof TMagicMethodClosingBrace,
            $token instanceof TAbstractMagicMethodEndSemicolon => 'magic',

            $token instanceof TFunctionClosingBrace,
            $token instanceof TAbstractMethodEndSemicolon => 'method',

            default => null,
        };
    }
}

<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Token\AMemberClosing;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;

class NormalizeMemberSpacing extends AMemberNormalizer
{
    public function __construct(
        private int $betweenConstants = 0,
        private int $betweenProperties = 0,
        private int $betweenEnumCases = 0,
        private int $betweenUseTraits = 0,
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

            $lastToken = $line->lastContentToken();

            if ($lastToken instanceof AMemberClosing) {
                $memberEndings[] = [
                    'index' => $i,
                    'type' => $lastToken->memberType(),
                ];
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
                $toRemove = $actual - $desired;

                for (
                    $r = count($blankIndices) - 1;
                    $r >= 0 && $toRemove > 0;
                    $r --, $toRemove --
                ) {
                    array_splice($lines, $blankIndices[$r], 1);
                }
            } elseif ($actual < $desired) {
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
            AMemberClosing::CONSTANT => $this->betweenConstants,
            AMemberClosing::PROPERTY => $this->betweenProperties,
            AMemberClosing::ENUM_CASE => $this->betweenEnumCases,
            AMemberClosing::USE_TRAIT => $this->betweenUseTraits,
            AMemberClosing::MAGIC_METHOD => $this->betweenMagicMethods,
            AMemberClosing::METHOD => $this->betweenMethods,
            default => 1,
        };
    }
}

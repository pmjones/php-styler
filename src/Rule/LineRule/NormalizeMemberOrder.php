<?php
declare(strict_types=1);

namespace PhpStyler\Rule\LineRule;

use PhpStyler\Line;
use PhpStyler\Token\AMemberClosing;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TBlankLine;

class NormalizeMemberOrder extends AMemberNormalizer
{
    /**
     * @param string[] $order
     */
    public function __construct(
        private array $order = [
            AMemberClosing::USE_TRAIT,
            AMemberClosing::ENUM_CASE,
            AMemberClosing::CONSTANT,
            AMemberClosing::PROPERTY,
            AMemberClosing::MAGIC_METHOD,
            AMemberClosing::METHOD,
        ],
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
            $lines = $this->reorderRegion($lines, $region);
        }

        return $lines;
    }

    /**
     * @param Line[] $lines
     * @param array{openIndex: int, closeIndex: int, memberIndent: int} $region
     * @return Line[]
     */
    private function reorderRegion(array $lines, array $region) : array
    {
        $regionStart = $region['openIndex'] + 1;
        $regionEnd = $region['closeIndex'] - 1;
        $memberIndent = $region['memberIndent'];

        if ($regionStart > $regionEnd) {
            return $lines;
        }

        // Find member-ending line indices
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
                    'closesStaticMember' => $lastToken->closesStaticMember,
                ];
            }
        }

        if (count($memberEndings) <= 1) {
            return $lines;
        }

        // Partition into blocks with precomputed sort keys
        $blocks = [];
        $blockStart = $regionStart;

        foreach ($memberEndings as $seqNum => $ending) {
            $blockEnd = $ending['index'];

            $blockLines = array_slice(
                $lines,
                $blockStart,
                $blockEnd - $blockStart + 1,
            );

            while ($blockLines !== [] && $blockLines[0]->isBlank()) {
                array_shift($blockLines);
            }

            $blocks[] = [
                'lines' => $blockLines,
                'group' => $this->memberGroup(
                    $ending['type'],
                    $ending['closesStaticMember'],
                ),
                'typeOrder' => $this->typeOrder($ending['type']),
                'originalIndex' => $seqNum,
            ];

            $blockStart = $blockEnd + 1;
        }

        // Collect trailing lines after the last member
        $trailingLines = [];

        if ($blockStart <= $regionEnd) {
            $trailingLines = array_slice(
                $lines,
                $blockStart,
                $regionEnd - $blockStart + 1,
            );

            while ($trailingLines !== [] && $trailingLines[0]->isBlank()) {
                array_shift($trailingLines);
            }
        }

        // Stable sort using precomputed keys
        $sorted = $blocks;

        usort(
            $sorted,
            fn ($a, $b)
                => $a['group'] <=> $b['group']
                    ?: $a['typeOrder'] <=> $b['typeOrder']
                    ?: $a['originalIndex'] <=> $b['originalIndex'],
        );

        // If order unchanged, leave lines untouched
        if (
            array_column($sorted, 'originalIndex')
            === array_column($blocks, 'originalIndex')
        ) {
            return $lines;
        }

        // Reassemble with blank lines based on token styles
        $newRegionLines = [];
        $refLine = $lines[$regionStart];
        $prevBlockLines = null;

        foreach ($sorted as $block) {
            if ($prevBlockLines !== null) {
                $lastLine = $prevBlockLines[array_key_last($prevBlockLines)];
                $lastToken = $lastLine->lastContentToken();

                if ($lastToken !== null && $lastToken->wantsBlankLineAfter()) {
                    $newRegionLines[] = $this->createBlankLine($refLine);
                }
            }

            foreach ($block['lines'] as $line) {
                $newRegionLines[] = $line;
            }

            $prevBlockLines = $block['lines'];
        }

        if ($trailingLines !== []) {
            $lastLine = $prevBlockLines[array_key_last($prevBlockLines)];
            $lastToken = $lastLine->lastContentToken();

            if ($lastToken !== null && $lastToken->wantsBlankLineAfter()) {
                $newRegionLines[] = $this->createBlankLine($refLine);
            }

            foreach ($trailingLines as $line) {
                $newRegionLines[] = $line;
            }
        }

        array_splice(
            $lines,
            $regionStart,
            $regionEnd - $regionStart + 1,
            $newRegionLines,
        );

        return $lines;
    }

    private function memberGroup(string $type, bool $closesStaticMember) : int
    {
        return match ($type) {
            AMemberClosing::USE_TRAIT,
            AMemberClosing::ENUM_CASE,
            AMemberClosing::CONSTANT => 0,

            default => $closesStaticMember ? 1 : 2,
        };
    }

    private function typeOrder(string $type) : int
    {
        $pos = array_search($type, $this->order, true);
        return $pos !== false ? (int) $pos : count($this->order);
    }

    private function createBlankLine(Line $refLine) : Line
    {
        return new Line(
            [new TBlankLine(AToken::SYNTHETIC, '')],
            0,
            $refLine->indentStr,
            $refLine->indentLen,
        );
    }
}

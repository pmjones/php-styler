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
use PhpStyler\Token\TStatic;
use PhpStyler\Token\TPropertyHooksAbstractClosingBrace;
use PhpStyler\Token\TPropertyHooksClosingBrace;
use PhpStyler\Token\TUseTraitClosingBrace;
use PhpStyler\Token\TUseTraitEndSemicolon;

class NormalizeMemberOrder extends ALineRule
{
    /**
     * @param string[] $order
     */
    public function __construct(
        private array $order = [
            'traituse',
            'enumcase',
            'const',
            'property',
            'magic',
            'method',
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
    private function reorderRegion(array $lines, array $region) : array
    {
        $openIndex = $region['openIndex'];
        $closeIndex = $region['closeIndex'];
        $memberIndent = $region['memberIndent'];
        $regionStart = $openIndex + 1;
        $regionEnd = $closeIndex - 1;

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

            $type = $this->memberEndType($line->lastContentToken());

            if ($type !== null) {
                $memberEndings[] = ['index' => $i, 'type' => $type];
            }
        }

        if (count($memberEndings) <= 1) {
            return $lines;
        }

        // Partition into blocks
        $blocks = [];
        $blockStart = $regionStart;

        foreach ($memberEndings as $seqNum => $ending) {
            $blockEnd = $ending['index'];

            $blockLines = array_slice(
                $lines,
                $blockStart,
                $blockEnd - $blockStart + 1,
            );

            // Strip leading blank lines
            while ($blockLines !== [] && $blockLines[0]->isBlank()) {
                array_shift($blockLines);
            }

            $blocks[] = [
                'lines' => $blockLines,
                'type' => $ending['type'],
                'isStatic' => $this->blockIsStatic($blockLines),
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

        // Stable sort by configured order
        $sorted = $blocks;

        usort(
            $sorted,
            function ($a, $b) {
                $aGroup = $this->memberGroup($a['type'], $a['isStatic']);
                $bGroup = $this->memberGroup($b['type'], $b['isStatic']);

                if ($aGroup !== $bGroup) {
                    return $aGroup <=> $bGroup;
                }

                $aOrder = $this->typeOrder($a['type']);
                $bOrder = $this->typeOrder($b['type']);

                if ($aOrder !== $bOrder) {
                    return $aOrder <=> $bOrder;
                }

                return $a['originalIndex'] <=> $b['originalIndex'];
            },
        );

        // If order unchanged, leave lines untouched
        if (
            array_column($sorted, 'originalIndex')
            === array_column($blocks, 'originalIndex')
        ) {
            return $lines;
        }

        $blocks = $sorted;

        // Reassemble with blank lines based on token styles
        $newRegionLines = [];
        $refLine = $lines[$regionStart];
        $lastBlock = null;

        foreach ($blocks as $block) {
            if ($lastBlock !== null) {
                $this->insertStyleBlankLine(
                    $lastBlock['lines'],
                    $refLine,
                    $newRegionLines,
                );
            }

            foreach ($block['lines'] as $line) {
                $newRegionLines[] = $line;
            }

            $lastBlock = $block;
        }

        if ($trailingLines !== []) {
            $this->insertStyleBlankLine(
                $lastBlock['lines'],
                $refLine,
                $newRegionLines,
            );

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

    /**
     * @param Line[] $blockLines
     * @param Line[] $newRegionLines
     */
    private function insertStyleBlankLine(
        array $blockLines,
        Line $refLine,
        array &$newRegionLines,
    ) : void
    {
        $lastLine = $blockLines[array_key_last($blockLines)] ?? null;
        $lastToken = $lastLine?->lastContentToken();

        if ($lastToken?->style?->blankLineAfter === true) {
            $newRegionLines[] = $this->createBlankLine($refLine);
        }
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

    /**
     * @param Line[] $blockLines
     */
    private function blockIsStatic(array $blockLines) : bool
    {
        foreach ($blockLines as $line) {
            foreach ($line->getTokens() as $token) {
                if ($token instanceof TStatic) {
                    return true;
                }
            }
        }

        return false;
    }

    private function memberGroup(string $type, bool $isStatic) : int
    {
        return match ($type) {
            'traituse', 'enumcase', 'const' => 0,
            default => $isStatic ? 1 : 2,
        };
    }

    private function typeOrder(string $type) : int
    {
        $pos = array_search($type, $this->order, true);
        return $pos !== false ? (int) $pos : count($this->order);
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

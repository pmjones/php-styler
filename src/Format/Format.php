<?php
declare(strict_types=1);

namespace PhpStyler\Format;

use PhpStyler\Rule\LineRule;
use PhpStyler\Rule\TokenRule;
use PhpStyler\Style\StyleLocator;

interface Format
{
    public function eol() : string;

    public function lineLen() : int;

    public function indentLen() : int;

    public function indentTab() : bool;

    public function styles() : StyleLocator;

    /** @return 'same_line'|'next_line' */
    public function classBracePosition() : string;

    /** @return 'same_line'|'next_line' */
    public function functionBracePosition() : string;

    /** @return 'same_line'|'next_line' */
    public function controlBracePosition() : string;

    /** @return 'lower'|'upper' */
    public function keywordCase() : string;

    public function concatenationSpacing() : bool;

    public function returnTypeColonSpacing() : bool;

    public function blankLineAfterBlock() : bool;

    /** @return array<TokenRule|LineRule> */
    public function rules() : array;
}

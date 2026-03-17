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

    /** @return array<TokenRule|LineRule> */
    public function rules() : array;
}

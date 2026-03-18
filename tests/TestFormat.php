<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DeclarationFormat;

class TestFormat extends DeclarationFormat
{
    public protected(set) array $rules = [];
}

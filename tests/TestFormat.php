<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\ExtendedFormat;

class TestFormat extends ExtendedFormat
{
    public protected(set) array $rules = [];
}

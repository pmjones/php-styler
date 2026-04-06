<?php
declare(strict_types=1);

namespace PhpStyler\Token;

interface AMemberClosing
{
    public const CONSTANT = 'CONSTANT';

    public const ENUM_CASE = 'ENUM_CASE';

    public const MAGIC_METHOD = 'MAGIC_METHOD';

    public const METHOD = 'METHOD';

    public const PROPERTY = 'PROPERTY';

    public const USE_TRAIT = 'USE_TRAIT';

    public bool $closesStaticMember { get; set; }

    public function memberType() : string;
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class AMemberClosing extends AToken
{
    public const CONSTANT = 'CONSTANT';

    public const ENUM_CASE = 'ENUM_CASE';

    public const MAGIC_METHOD = 'MAGIC_METHOD';

    public const METHOD = 'METHOD';

    public const PROPERTY = 'PROPERTY';

    public const USE_TRAIT = 'USE_TRAIT';

    public bool $closesStaticMember = false;

    abstract public function memberType() : string;
}

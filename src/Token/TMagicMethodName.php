<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TMagicMethodName extends TFunctionName
{
    public const NAMES = [
        '__construct',
        '__destruct',
        '__call',
        '__callStatic',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__sleep',
        '__wakeup',
        '__serialize',
        '__unserialize',
        '__toString',
        '__invoke',
        '__set_state',
        '__clone',
        '__debugInfo',
    ];
}

<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Line;

class TFunctionCallName extends AToken
{
    /** @var ?array<string, int> */
    private static ?array $nativeFunctions = null;

    public function render(Line $line) : string
    {
        self::$nativeFunctions ??= array_flip(get_defined_functions()['internal']);

        if (isset(self::$nativeFunctions[strtolower($this->text)])) {
            return strtolower($this->text);
        }

        return $this->text;
    }
}

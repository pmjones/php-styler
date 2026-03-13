<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TUnknownStringTest extends TTestCase
{
    /**
     * @inheritdoc
     */
    public static function provide() : array
    {
        /** @php-styler-expansive */
        return [
            'basic' => [
                <<<'CODE'
                <?php
                $foo = FOO;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TUnknownString::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

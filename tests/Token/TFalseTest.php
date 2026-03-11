<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TFalseTest extends TTestCase
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
                $foo = false;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TFalse::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}

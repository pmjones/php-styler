<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TNullTest extends TTestCase
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
                $foo = null;
                CODE,
                [
                    TPhpOpeningTag::class,
                    TVariable::class,
                    TAssign::class,
                    TNull::class,
                    TSemicolon::class,
                ],
            ],
        ];
    }
}
